<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Metric; // Importamos el modelo de Métricas

class DashboardController extends Controller
{
    public function index(): View
    {
        $rider = Auth::guard('rider')->user();

        // Buscamos la asignación y cuenta activas del rider
        $activeAssignment = $rider->activeAssignment()->with('account')->first();

        // Inicializamos los KPIs con valores por defecto
        $kpis = [
            'last_day_orders' => 0,
            'last_day_cdt' => 0,
            'last_7_days_orders' => 0,
            'last_30_days_orders' => 0,
            'latest_date' => null,
            'has_metrics' => false,
        ];

        // Solo calculamos las métricas si el rider tiene una cuenta asignada
        if ($activeAssignment && $activeAssignment->account) {
            $courierId = $activeAssignment->account->courier_id;

            // 1. Buscamos el registro de métrica más reciente para este courier_id
            $latestMetric = Metric::where('courier_id', $courierId)
                ->orderBy('fecha', 'desc')
                ->first();

            // 2. Si encontramos métricas, procedemos a calcular los KPIs
            if ($latestMetric) {
                $kpis['has_metrics'] = true;
                $latestDate = $latestMetric->fecha; // Es un objeto Carbon gracias al 'cast' en el modelo
                $kpis['latest_date'] = $latestDate->translatedFormat('d \d\e F');

                // KPIs del último día de actividad
                $kpis['last_day_orders'] = $latestMetric->pedidos_entregados;
                $kpis['last_day_cdt'] = $latestMetric->tiempo_promedio;

                // KPIs de los últimos 7 días (desde la última fecha disponible hacia atrás)
                $sevenDaysAgo = $latestDate->copy()->subDays(6);
                $kpis['last_7_days_orders'] = Metric::where('courier_id', $courierId)
                    ->whereBetween('fecha', [$sevenDaysAgo, $latestDate])
                    ->sum('pedidos_entregados');

                // KPIs de los últimos 30 días (desde la última fecha disponible hacia atrás)
                $thirtyDaysAgo = $latestDate->copy()->subDays(29);
                $kpis['last_30_days_orders'] = Metric::where('courier_id', $courierId)
                    ->whereBetween('fecha', [$thirtyDaysAgo, $latestDate])
                    ->sum('pedidos_entregados');
            }
        }

        return view('content.rider.dashboard.index', compact('rider', 'activeAssignment', 'kpis'));
    }
}
