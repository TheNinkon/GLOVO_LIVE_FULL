<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prefactura;
use App\Models\PrefacturaItem;
use App\Models\PrefacturaAssignment;
use App\Models\Rider;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class PrefacturaController extends Controller
{
    /**
     * Muestra la vista principal de prefacturas.
     */
    public function index()
    {
        $prefacturas = Prefactura::withCount('items')->latest()->get();
        return view('content.admin.prefacturas.index', compact('prefacturas'));
    }

    /**
     * Muestra los detalles de una prefactura con sus métricas.
     */
    public function show(Prefactura $prefactura)
    {
        // CORRECCIÓN: Se elimina la carga de la relación 'rider' en el item.
        // Ahora se carga solo en la asignación.
        $prefactura->load(['items' => function($query) {
            $query->with(['assignments' => function($q) {
                $q->with('rider')->orderByDesc('created_at');
            }])->orderBy('courier_id');
        }]);

        // CÁLCULO DE LAS MÉTRICAS DE RESUMEN
        $metrics = $prefactura->items()
            ->selectRaw('SUM(cash_out) as total_cash_out, SUM(tips) as total_tips')
            ->first();

        $assignedMetrics = PrefacturaAssignment::whereHas('item', function($query) use ($prefactura) {
            $query->where('prefactura_id', $prefactura->id);
        })->selectRaw('SUM(CASE WHEN type = "cash_out" THEN amount ELSE 0 END) as total_cash_out_assigned,
                       SUM(CASE WHEN type = "tips" THEN amount ELSE 0 END) as total_tips_assigned')
           ->first();

        $metrics->cash_out_pending = $metrics->total_cash_out - ($assignedMetrics->total_cash_out_assigned ?? 0);
        $metrics->tips_pending = $metrics->total_tips - ($assignedMetrics->total_tips_assigned ?? 0);

        $riders = Rider::all()->sortBy('full_name')->pluck('full_name', 'id');

        return view('content.admin.prefacturas.show', compact('prefactura', 'riders', 'metrics'));
    }

    /**
     * Crea un nuevo período de prefactura y sube el CSV.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_at' => 'required|date_format:Y-m-d',
            'end_at' => 'required|date_format:Y-m-d|after_or_equal:start_at',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $prefactura = Prefactura::create([
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        $file = $request->file('csv_file');
        $csvData = file_get_contents($file->getRealPath());
        $lines = array_map('trim', explode("\n", $csvData));
        $headers = str_getcsv(strtolower(array_shift($lines)));

        $processedCount = 0;
        foreach ($lines as $line) {
            $row = str_getcsv($line);
            if (count($row) < 3) continue;

            $data = @array_combine($headers, $row);
            if (!$data || empty($data['id'])) continue;

            $toFloat = fn($v) => floatval(str_replace(['_','€',' ', ','], ['', '', '', '.'], $v));

            $prefactura->items()->create([
                'courier_id' => trim($data['id']),
                'cash_out' => $toFloat($data['courier cash out'] ?? 0),
                'tips' => $toFloat($data['courier tips'] ?? 0),
            ]);
            $processedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Prefactura creada y datos procesados.',
            'prefactura_id' => $prefactura->id,
            'processed_items' => $processedCount
        ]);
    }

    /**
     * Asigna un monto parcial de cash_out o tips a un rider.
     */
    public function assignRider(Request $request, PrefacturaItem $item): JsonResponse
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:cash_out,tips'
        ]);

        $itemAssignedAmount = $item->assignments()->where('type', $request->type)->sum('amount');
        $remaining = ($request->type == 'cash_out') ? $item->cash_out - $itemAssignedAmount : $item->tips - $itemAssignedAmount;

        if ($request->amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => "El monto a asignar excede el restante de {$remaining} €."
            ], 422);
        }

        PrefacturaAssignment::create([
            'prefactura_item_id' => $item->id,
            'rider_id' => $request->rider_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => "Asignación de {$request->amount} € a {$item->courier_id} completada.",
        ]);
    }

    /**
     * Actualiza el estado de una asignación.
     */
    public function updateAssignmentStatus(Request $request, PrefacturaAssignment $assignment): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,deducted'
        ]);

        $assignment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => "El estado de la asignación ha sido actualizado a '{$assignment->status}'.",
            'assignment' => $assignment
        ]);
    }
}
