<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class RiderMetricsController extends Controller
{
    /**
     * Muestra la vista principal de Métricas para el rider autenticado.
     */
    public function index()
    {
        // Se asume que el rider ya está autenticado
        $rider = auth('rider')->user();
        if (!$rider) {
            abort(403, 'No estás autenticado como rider.');
        }

        // Recuperar los transportes disponibles para el filtro
        $transports = Metric::query()
            ->whereNotNull('transport')
            ->distinct()
            ->orderBy('transport')
            ->pluck('transport');

        return view('content.rider.metrics.index', compact('rider', 'transports'));
    }

    /**
     * Devuelve la lista paginada de métricas (JSON) con filtros para el rider autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $rider = auth('rider')->user();
            if (!$rider) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
            }

            $query = Metric::query()->from('glovo_metrics as m')
                ->select(
                    'm.fecha',
                    'm.pedidos_entregados',
                    'm.horas',
                    'm.cancelados',
                    'm.reasignaciones',
                    'm.tiempo_promedio',
                    DB::raw("m.pedidos_entregados / m.horas as eficiencia")
                )
                ->join('accounts as a', 'a.courier_id', '=', 'm.courier_id')
                ->join('assignments as ass', function ($join) {
                    $join->on('ass.account_id', '=', 'a.id')
                        ->whereRaw('m.fecha BETWEEN ass.start_at AND COALESCE(ass.end_at, m.fecha)');
                })
                ->where('ass.rider_id', $rider->id);

            // ---- Filtros ----
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
                $query->whereBetween('m.fecha', [$dateFrom, $dateTo]);
            }
            if ($request->filled('transport')) {
                $query->where('m.transport', $request->transport);
            }
            if ($request->filled('weekday')) {
                $query->whereRaw('DAYOFWEEK(m.fecha) = ?', [$request->weekday]);
            }

            $perPage = (int) $request->input('per_page', 15);
            $perPage = max(1, min($perPage, 100));

            $metrics = $query
                ->orderByDesc('m.fecha')
                ->paginate($perPage)
                ->appends($request->all());

            return response()->json($metrics);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud. Por favor, revisa el rango de fechas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * KPIs agregados (JSON) respetando los mismos filtros para el rider autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function kpis(Request $request): JsonResponse
    {
        try {
            $rider = auth('rider')->user();
            if (!$rider) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
            }

            $query = DB::table('glovo_metrics as m')
                ->join('accounts as a', 'a.courier_id', '=', 'm.courier_id')
                ->join('assignments as ass', function ($join) {
                    $join->on('ass.account_id', '=', 'a.id')
                         ->whereRaw('m.fecha BETWEEN ass.start_at AND COALESCE(ass.end_at, m.fecha)');
                })
                ->where('ass.rider_id', $rider->id);

            // ---- Filtros dinámicos ----
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
                $query->whereBetween('m.fecha', [$dateFrom, $dateTo]);
            }
            if ($request->filled('transport')) {
                $query->where('m.transport', $request->transport);
            }
            if ($request->filled('weekday')) {
                $query->whereRaw('DAYOFWEEK(m.fecha) = ?', [$request->weekday]);
            }

            $stats = $query->selectRaw('
                SUM(pedidos_entregados) as total_orders,
                SUM(horas) as total_hours,
                AVG(cancelados) as avg_canceled,
                AVG(reasignaciones) as avg_reassignments,
                AVG(tiempo_promedio) as avg_cdt
            ')->first();

            $totalOrders = (float) ($stats->total_orders ?? 0);
            $totalHours  = (float) ($stats->total_hours ?? 0);
            $avgCanceled = (float) ($stats->avg_canceled ?? 0);
            $avgReassign = (float) ($stats->avg_reassignments ?? 0);
            $avgCdt      = (float) ($stats->avg_cdt ?? 0);

            $avgRatio = $totalHours > 0 ? ($totalOrders / $totalHours) : 0.0;

            return response()->json([
                'total_orders'      => round($totalOrders, 2),
                'avg_ratio'         => round($avgRatio, 2),
                'avg_canceled'      => round($avgCanceled, 2),
                'avg_reassignments' => round($avgReassign, 2),
                'avg_cdt'           => round($avgCdt, 2),
                'total_hours'       => round($totalHours, 2),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud. Por favor, revisa el rango de fechas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
