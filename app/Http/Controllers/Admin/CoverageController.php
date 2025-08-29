<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Rider;
use App\Models\Schedule; // 👈 Importación del modelo Schedule
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class CoverageController extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale(config('app.locale'));

        // Obtener todas las ciudades disponibles a partir de los forecasts.
        $availableCities = Forecast::distinct()->pluck('city')->sort();

        // Determinar la ciudad y semana seleccionadas
        $selectedCity = $request->input('city') ?? $availableCities->first();
        try {
            $startOfWeek = $request->input('week') ? Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        // Definir la variable de navegación para la vista
        $nav = [
            'prev' => route('admin.coverage.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->subWeek()->format('Y-m-d')]),
            'next' => route('admin.coverage.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->addWeek()->format('Y-m-d')]),
            'current' => $startOfWeek->translatedFormat('j M') . ' - ' . $endOfWeek->translatedFormat('j M, Y'),
        ];

        $days = [];
        $day = $startOfWeek->clone();
        for ($i = 0; $i < 7; $i++) {
            $days[] = [
                'name' => $day->translatedFormat('D'),
                'date' => $day->format('j/m'),
                'key' => strtolower($day->translatedFormat('D')),
            ];
            $day->addDay();
        }

        $timeSlots = [];
        for ($i = 0; $i < 48; $i++) {
            $timeSlots[] = Carbon::createFromTime(0, 0, 0)->addMinutes(30 * $i)->format('H:i');
        }

        $coverageData = null;
        $forecast = Forecast::where('city', $selectedCity)->where('week_start_date', $startOfWeek)->first();

        if ($forecast) {
            $riders = Rider::where('city', $selectedCity)->where('status', 'active')->get();
            $schedules = Schedule::where('forecast_id', $forecast->id)->get();

            $coverageData = [];
            foreach ($days as $day) {
                foreach ($timeSlots as $time) {
                    $coverageData[$day['key']][$time] = ['demand' => $forecast->forecast_data[strtolower($day['key'])][$time] ?? 0, 'booked' => 0];
                }
            }

            foreach ($schedules as $schedule) {
                $dayKey = strtolower(Carbon::parse($schedule->slot_date)->translatedFormat('D'));
                $timeKey = Carbon::parse($schedule->slot_time)->format('H:i');
                if (isset($coverageData[$dayKey][$timeKey])) {
                    $coverageData[$dayKey][$timeKey]['booked']++;
                }
            }
        }

        return view('content.admin.coverage.index', compact('availableCities', 'selectedCity', 'nav', 'startOfWeek', 'days', 'timeSlots', 'coverageData'));
    }
}
