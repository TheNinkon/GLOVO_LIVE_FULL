<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class MetricController extends Controller
{
    /**
     * Muestra la vista principal de Métricas con filtros precargados.
     */
    public function index()
    {
        $cities = Metric::query()
            ->whereNotNull('ciudad')
            ->distinct()
            ->orderBy('ciudad')
            ->pluck('ciudad');

        $transports = Metric::query()
            ->whereNotNull('transport')
            ->distinct()
            ->orderBy('transport')
            ->pluck('transport');

        $riders = Rider::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->pluck('full_name', 'full_name');

        return view('content.admin.metrics.index', compact('cities', 'transports', 'riders'));
    }

    /**
     * Lista paginada de métricas (JSON) con filtros.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $query = Metric::query()->from('glovo_metrics as m')
                ->select(
                    'm.*',
                    DB::raw("COALESCE(r.full_name, 'Sin Asignar') as rider_name")
                )
                ->leftJoin('accounts as a', 'a.courier_id', '=', 'm.courier_id')
                ->leftJoin('assignments as ass', function ($join) {
                    $join->on('ass.account_id', '=', 'a.id')
                        ->whereRaw('m.fecha BETWEEN ass.start_at AND COALESCE(ass.end_at, m.fecha)');
                })
                ->leftJoin('riders as r', 'r.id', '=', 'ass.rider_id');

            // ---- Filtros ----
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
                $query->whereBetween('m.fecha', [$dateFrom, $dateTo]);
            }
            if ($request->filled('city')) {
                $query->where('m.ciudad', $request->city);
            }
            if ($request->filled('transport')) {
                $query->where('m.transport', $request->transport);
            }
            if ($request->filled('courier_id')) {
                $query->where('m.courier_id', 'like', '%' . $request->courier_id . '%');
            }
            if ($request->filled('rider_name')) {
                $query->where('r.full_name', 'like', '%' . $request->rider_name . '%');
            }
            if ($request->filled('weekday')) {
                $query->whereRaw('DAYOFWEEK(m.fecha) = ?', [$request->weekday]);
            }

            $perPage = (int) $request->input('per_page', 15);
            $perPage = max(1, min($perPage, 100));

            $metrics = $query
                ->orderByDesc('m.fecha')
                ->orderBy('m.courier_id')
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
     * KPIs agregados (JSON) respetando los mismos filtros.
     */
    public function kpis(Request $request): JsonResponse
    {
        try {
            $query = Metric::query()->from('glovo_metrics as m');

            if ($request->filled('rider_name')) {
                $query->leftJoin('accounts as a', 'a.courier_id', '=', 'm.courier_id')
                      ->leftJoin('assignments as ass', function ($join) {
                          $join->on('ass.account_id', '=', 'a.id')
                               ->whereRaw('m.fecha BETWEEN ass.start_at AND COALESCE(ass.end_at, m.fecha)');
                      })
                      ->leftJoin('riders as r', 'r.id', '=', 'ass.rider_id');
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
                $query->whereBetween('fecha', [$dateFrom, $dateTo]);
            }
            if ($request->filled('city')) {
                $query->where('ciudad', $request->city);
            }
            if ($request->filled('transport')) {
                $query->where('transport', $request->transport);
            }
            if ($request->filled('courier_id')) {
                $query->where('courier_id', 'like', '%' . $request->courier_id . '%');
            }
            if ($request->filled('rider_name')) {
                $query->where('r.full_name', 'like', '%' . $request->rider_name . '%');
            }
            if ($request->filled('weekday')) {
                $query->whereRaw('DAYOFWEEK(fecha) = ?', [$request->weekday]);
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

            $costoPedido = (float) $request->input('cost_per_order', 5.50);
            $costoHora   = (float) $request->input('cost_per_hour', 12.00);

            $gananciaTotal = $totalOrders * $costoPedido;
            $costoTotal    = $totalHours * $costoHora;
            $utilidad      = $gananciaTotal - $costoTotal;

            return response()->json([
                'total_orders'      => round($totalOrders, 2),
                'avg_ratio'         => round($avgRatio, 2),
                'avg_canceled'      => round($avgCanceled, 2),
                'avg_reassignments' => round($avgReassign, 2),
                'avg_cdt'           => round($avgCdt, 2),
                'total_hours'       => round($totalHours, 2),
                'costo_total'       => round($costoTotal, 2),
                'ganancia_total'    => round($gananciaTotal, 2),
                'utilidad'          => round($utilidad, 2),
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
