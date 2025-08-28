<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\PrefacturaAssignment;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.admin.riders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.admin.riders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:riders,email',
            'phone' => 'nullable|string|max:20',
        ]);

        Rider::create($request->all());

        return redirect()->route('admin.riders.index')->with('success', 'Rider creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
   public function show(Rider $rider)
    {
        // Precargamos las relaciones para evitar problemas N+1
        $rider->load(['assignments.account']);

        $transports = Metric::query()
            ->whereNotNull('transport')
            ->distinct()
            ->orderBy('transport')
            ->pluck('transport');

        $cities = Metric::query()
            ->whereNotNull('ciudad')
            ->distinct()
            ->orderBy('ciudad')
            ->pluck('ciudad');

        return view('content.admin.riders.show', compact('rider', 'transports', 'cities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rider $rider)
    {
        return view('content.admin.riders.edit', compact('rider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rider $rider)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:riders,email,' . $rider->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $rider->update($request->all());

        return redirect()->route('admin.riders.index')->with('success', 'Rider actualizado exitosamente.');
    }

    /**
     * Get riders list for Datatables and KPIs.
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $query = Rider::query()
                ->select(
                    'id',
                    'full_name',
                    'email',
                    'phone',
                    'city',
                    'status',
                    'start_date',
                    'created_at'
                );

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }

            $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                  ->orderByDesc('created_at');

            $perPage = (int) $request->input('length', 10);
            $riders = $query->paginate($perPage);

            $kpis = [
                'total' => Rider::count(),
                'active' => Rider::where('status', 'active')->count(),
                'inactive' => Rider::where('status', 'inactive')->count(),
                'pending' => Rider::where('status', 'pending')->count(),
                'blocked' => Rider::where('status', 'blocked')->count(),
            ];

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $riders->total(),
                'recordsFiltered' => $riders->total(),
                'data' => $riders->items(),
                'kpis' => $kpis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prefactura assignments for a rider.
     * Retorna datos paginados en el formato correcto para DataTables.
     */
        public function getPrefacturaAssignments(Request $request, Rider $rider): JsonResponse
    {
        try {
            $query = $rider->prefacturaAssignments();

            $query->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            });

            $query->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->type);
            });

            $assignments = $query->latest()->paginate((int) $request->input('length', 10));

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $assignments->total(),
                'recordsFiltered' => $assignments->total(),
                'data' => $assignments->items(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cargar asignaciones.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Glovo metrics for a rider.
     * Retorna datos paginados en el formato correcto para DataTables.
     */
        public function getGlovoMetrics(Request $request, Rider $rider): JsonResponse
    {
        try {
            // El método glovoMetrics() del modelo Rider ahora contiene la lógica correcta
            $metrics = $rider->glovoMetrics()->paginate((int) $request->input('length', 10));

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $metrics->total(),
                'recordsFiltered' => $metrics->total(),
                'data' => $metrics->items(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cargar métricas.', 'error' => $e->getMessage()], 500);
        }
    }
}
