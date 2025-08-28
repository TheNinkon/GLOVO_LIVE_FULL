<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class CoverageController extends Controller
{
    public function index($city = null, $week = null): View
    {
        Carbon::setLocale(config('app.locale'));
        $availableCities = Forecast::distinct()->pluck('city')->sort();

        if ($availableCities->isEmpty()) {
            return view('content.admin.coverage.index')->with('error', 'No hay forecasts subidos en el sistema.');
        }

        $selectedCity = $city ?? $availableCities->first();

        try {
            $startOfWeek = $week ? Carbon::parse($week)->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }

        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        $forecast = Forecast::where('city', $selectedCity)
            ->where('week_start_date', $startOfWeek)
            ->first();

        $nav = [
            'prev' => route('admin.coverage.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->subWeek()->format('Y-m-d')]),
            'next' => route('admin.coverage.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->addWeek()->format('Y-m-d')]),
            'current' => $startOfWeek->translatedFormat('j M') . ' - ' . $endOfWeek->translatedFormat('j M, Y'),
        ];

        if (!$forecast) {
            return view('content.admin.coverage.index', compact('selectedCity', 'nav', 'availableCities', 'startOfWeek', 'endOfWeek'))
                ->with('error', "No se encontró un forecast para la ciudad de {$selectedCity} en esta semana.");
        }

        $bookedSlots = Schedule::where('forecast_id', $forecast->id)
            ->selectRaw('slot_date, slot_time, COUNT(*) as count')
            ->groupBy('slot_date', 'slot_time')
            ->get()
            ->keyBy(fn($item) => $item->slot_date->format('Y-m-d') . '_' . $item->slot_time->format('H:i:s'));

        $days = [];
        for ($i=0; $i < 7; $i++) {
            $currentDate = $startOfWeek->clone()->addDays($i);
            $days[] = [
                'key' => strtolower($currentDate->format('D')),
                'name' => $currentDate->translatedFormat('D'),
                'date' => $currentDate->format('d/m'),
                'full_date' => $currentDate->format('Y-m-d'),
            ];
        }

        $timeSlots = collect(CarbonPeriod::create('00:00', '30 minutes', '23:30'))->map(fn ($time) => $time->format('H:i'));

        $coverageData = [];
        foreach ($days as $day) {
            foreach ($timeSlots as $time) {
                $timeCarbon = Carbon::parse($time);
                $slotIdentifier = $day['full_date'] . '_' . $timeCarbon->format('H:i:s');
                $demand = $forecast->forecast_data[$day['key']][$time] ?? 0;
                $booked = $bookedSlots[$slotIdentifier]->count ?? 0;
                $coverageData[$day['key']][$time] = ['demand' => $demand, 'booked' => $booked];
            }
        }

        return view('content.admin.coverage.index', compact(
            'coverageData',
            'selectedCity',
            'nav',
            'availableCities',
            'startOfWeek',
            'endOfWeek',
            'days',
            'timeSlots'
        ));
    }
}
