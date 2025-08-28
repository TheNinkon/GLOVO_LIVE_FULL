<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Carbon\Carbon;

class ForecastController extends Controller
{
    public function index()
    {
        $forecasts = Forecast::orderBy('week_start_date', 'desc')->paginate(15);
        return view('content.admin.forecasts.index', compact('forecasts'));
    }

    public function create()
    {
        return view('content.admin.forecasts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'week_start_date' => 'required|date',
            'forecast_file' => 'required|file|mimes:csv,txt',
            'booking_deadline' => 'required|date',
        ]);

        $path = $request->file('forecast_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // La primera fila es la cabecera

        $records = $csv->getRecords();
        $forecastData = [
            'mon' => [], 'tue' => [], 'wed' => [], 'thu' => [],
            'fri' => [], 'sat' => [], 'sun' => [],
        ];

        foreach ($records as $record) {
            $time = Carbon::parse($record['Etiquetas de fila'])->format('H:i');
            $forecastData['mon'][$time] = (int)$record['Mon'];
            $forecastData['tue'][$time] = (int)$record['Tue'];
            $forecastData['wed'][$time] = (int)$record['Wed'];
            $forecastData['thu'][$time] = (int)$record['Thu'];
            $forecastData['fri'][$time] = (int)$record['Fri'];
            $forecastData['sat'][$time] = (int)$record['Sat'];
            $forecastData['sun'][$time] = (int)$record['Sun'];
        }

        Forecast::updateOrCreate(
            [
                'city' => $request->city,
                'week_start_date' => Carbon::parse($request->week_start_date)->startOfWeek(),
                'booking_deadline' => $request->booking_deadline, // <-- GUARDAR EL CAMPO
            ],
            ['forecast_data' => $forecastData]
        );

        return redirect()->route('admin.forecasts.index')->with('success', 'Forecast importado correctamente.');
    }
}
