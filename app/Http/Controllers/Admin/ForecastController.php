<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\View\View;

class ForecastController extends Controller
{
    public function __construct()
    {
        // Aplica la política ForecastPolicy a todos los métodos del controlador.
        $this->authorizeResource(Forecast::class, 'forecast');
    }

    public function index(Request $request): View
    {
        Carbon::setLocale(config('app.locale'));

        $availableCities = Forecast::distinct()->pluck('city')->sort();
        $selectedCity = $request->input('city') ?? $availableCities->first();

        try {
            $startOfWeek = $request->input('week') ? Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        // Se define la variable $nav para la paginación de semanas.
        $nav = [
            'prev' => route('admin.forecasts.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->subWeek()->format('Y-m-d')]),
            'next' => route('admin.forecasts.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->addWeek()->format('Y-m-d')]),
            'current' => "Semana del " . $startOfWeek->translatedFormat('j M') . ' - ' . $endOfWeek->translatedFormat('j M, Y'),
        ];

        $forecasts = Forecast::where('city', $selectedCity)
            ->when($startOfWeek, fn ($query) => $query->where('week_start_date', $startOfWeek))
            ->orderBy('week_start_date', 'desc')
            ->paginate(15);

        // Pasa todas las variables necesarias a la vista para evitar errores.
        return view('content.admin.forecasts.index', compact('forecasts', 'selectedCity', 'nav', 'availableCities', 'startOfWeek'));
    }

    public function create(): View
    {
        // Se define la variable $availableCities y $startOfWeek para la vista de creación.
        $availableCities = Forecast::distinct()->pluck('city')->sort();
        $selectedCity = $availableCities->first() ?? 'Selecciona una ciudad';
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return view('content.admin.forecasts.create', compact('selectedCity', 'availableCities', 'startOfWeek'));
    }

    public function store(Request $request)
    {
        // ... (Tu código para el store)
    }

    public function destroy(Forecast $forecast)
    {
        $forecast->delete();
        return redirect()->route('admin.forecasts.index')->with('success', 'Forecast y horas asociadas eliminados correctamente.');
    }
}
