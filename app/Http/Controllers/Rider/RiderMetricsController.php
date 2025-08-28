<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metric;
use App\Models\Assignment;
use App\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RiderMetricsController extends Controller
{
    /**
     * Muestra la vista de métricas diarias del rider.
     */
    public function index()
    {
        return view('content.rider.metrics.index');
    }

    /**
     * Devuelve las métricas diarias del rider con filtros.
     */
    public function list(Request $request): JsonResponse
    {
        $rider = auth('rider')->user();

        try {
            // Unir la tabla de métricas de Glovo con las asignaciones del rider
            $query = Metric::query()->from('glovo_metrics as m')
                ->join('accounts as a', 'a.courier_id', '=', 'm.courier_id')
                ->join('assignments as ass', function ($join) {
                    $join->on('ass.account_id', '=', 'a.id')
                         ->whereRaw('m.fecha BETWEEN ass.start_at AND COALESCE(ass.end_at, m.fecha)');
                })
                ->where('ass.rider_id', $rider->id)
                ->select(
                    'm.fecha',
                    'm.ciudad',
                    'm.pedidos_entregados',
                    'm.horas',
                    'm.tiempo_promedio'
                )
                ->distinct('m.fecha') // Agrupar por fecha para evitar duplicados
                ->orderByDesc('m.fecha');

            // Filtro por rango de fechas si se proporciona
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
                $query->whereBetween('m.fecha', [$dateFrom, $dateTo]);
            }

            $perPage = (int) $request->input('per_page', 15);
            $perPage = max(1, min($perPage, 100));

            $metrics = $query->paginate($perPage)->appends($request->all());

            return response()->json($metrics);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al cargar las métricas. ' . $e->getMessage()], 500);
        }
    }
}
