<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ForecastController extends Controller
{
    public function __construct()
    {
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

        $nav = [
            'prev' => route('admin.forecasts.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->subWeek()->format('Y-m-d')]),
            'next' => route('admin.forecasts.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->addWeek()->format('Y-m-d')]),
            'current' => "Semana del " . $startOfWeek->translatedFormat('j M') . ' - ' . $endOfWeek->translatedFormat('j M, Y'),
        ];

        $forecasts = Forecast::where('city', $selectedCity)
            ->when($startOfWeek, fn ($query) => $query->where('week_start_date', $startOfWeek))
            ->orderBy('week_start_date', 'desc')
            ->paginate(15);

        return view('content.admin.forecasts.index', compact('forecasts', 'selectedCity', 'nav', 'availableCities', 'startOfWeek'));
    }

    public function create(): View
    {
        $this->authorize('create', Forecast::class);

        $availableCities = Forecast::distinct()->pluck('city')->sort();
        $selectedCity = $availableCities->first() ?? 'Selecciona una ciudad';
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return view('content.admin.forecasts.create', compact('selectedCity', 'availableCities', 'startOfWeek'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'week_start_date' => 'required|date',
            'booking_deadline' => 'required|date',
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.required' => 'Debes subir un archivo CSV.',
            'file.mimes' => 'El archivo debe ser de tipo CSV.',
            'city.required' => 'Debes seleccionar una ciudad.',
            'week_start_date.required' => 'Debes seleccionar la fecha de inicio de semana.',
            'booking_deadline.required' => 'Debes seleccionar la fecha límite para reservar.',
        ]);

        $file = $request->file('file');

        try {
            DB::beginTransaction();

            $week_start_date = Carbon::parse($request->week_start_date);

            // VERIFICACIÓN CLAVE: Evita el error de duplicidad
            if (Forecast::where('city', $request->city)->where('week_start_date', $week_start_date)->exists()) {
                throw new \Exception('Ya existe un forecast para la ciudad y semana seleccionadas. Elimina el anterior o selecciona otra semana.');
            }

            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();

            $forecast_data = [];
            // Los encabezados del archivo CSV de tu ejemplo
            $dayHeaders = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $timeHeader = 'Etiquetas de fila';

            foreach ($records as $record) {
                // Asegúrate de que el CSV tiene la columna de tiempo
                if (!isset($record[$timeHeader])) {
                    throw new \Exception('La columna de tiempo "Etiquetas de fila" no se encontró en el archivo CSV.');
                }

                $timeSlot = $record[$timeHeader];
                $timeSlot = str_replace(':00:00', ':00', $timeSlot);

                // Itera sobre los días de la semana y guarda el valor en el array
                foreach ($dayHeaders as $day) {
                    $demand = $record[$day] ?? 0;
                    $forecast_data[strtolower($day)][$timeSlot] = (int) $demand;
                }
            }

            Forecast::create([
                'city' => $request->city,
                'week_start_date' => $week_start_date,
                'booking_deadline' => Carbon::parse($request->booking_deadline),
                'forecast_data' => $forecast_data,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Ocurrió un error al procesar el archivo: ' . $e->getMessage());
        }

        return redirect()->route('admin.forecasts.index')->with('success', 'Forecast importado y procesado correctamente.');
    }

    public function destroy(Forecast $forecast): RedirectResponse
    {
        $this->authorize('delete', $forecast);

        $forecast->delete();
        return redirect()->route('admin.forecasts.index')->with('success', 'Forecast y horas asociadas eliminados correctamente.');
    }
}
