<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountStoreRequest;
use App\Http\Requests\Admin\AccountUpdateRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    /**
     * Aplica la policy a todas las acciones REST del recurso.
     */
    public function __construct()
    {
        $this->authorizeResource(Account::class, 'account');
    }

    /**
     * Listado de cuentas (vista y DataTables AJAX).
     * Cargamos la relación activeAssignment.rider para evitar N+1.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Account::class);

        if ($request->ajax()) {
            $data = Account::with('activeAssignment.rider')->select('accounts.*');

            return DataTables::of($data)
                ->addColumn('assigned_to', function ($row) {
                    if ($row->activeAssignment && $row->activeAssignment->rider) {
                        // El nombre del rider ahora es un enlace al historial
                        return '<a href="' . route('admin.accounts.show', $row->id) . '">' . e($row->activeAssignment->rider->full_name) . '</a>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl   = route('admin.accounts.show', $row->id);
                    $editUrl   = route('admin.accounts.edit', $row->id);
                    $deleteUrl = route('admin.accounts.destroy', $row->id);
                    $assignUrl = route('admin.assignments.create', $row->id);

                    // Botones por defecto
                    $showBtn   = '<a href="' . $showUrl . '" class="btn btn-sm btn-icon" title="Ver"><i class="ti tabler-eye"></i></a>';
                    $editBtn   = '<a href="' . $editUrl . '" class="btn btn-sm btn-icon item-edit" title="Editar"><i class="ti tabler-pencil"></i></a>';
                    $deleteBtn = '<a href="javascript:;" class="btn btn-sm btn-icon delete-account-btn" data-url="' . $deleteUrl . '" title="Eliminar"><i class="ti tabler-trash"></i></a>';

                    // Si tiene asignación activa, permitir finalizarla; si no, permitir asignar
                    if ($row->activeAssignment) {
                        $endUrl = route('admin.assignments.end', $row->activeAssignment->id);
                        $endBtn = '<form action="' . $endUrl . '" method="POST" class="d-inline ms-1">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-sm btn-warning">Finalizar</button>
                                   </form>';

                        // En modo con asignación activa dejamos ver y finalizar (sin editar/eliminar para forzar flujo)
                        return $showBtn . $endBtn;
                    }

                    // Sin asignación activa: Asignar + acciones CRUD
                    $assignBtn = '<a href="' . $assignUrl . '" class="btn btn-sm btn-primary me-1">Asignar</a> ';
                    return $assignBtn . $showBtn . $editBtn . $deleteBtn;
                })
                ->rawColumns(['action', 'assigned_to'])
                ->make(true);
        }

        return view('content.admin.accounts.index');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('content.admin.accounts.create');
    }

    /**
     * Guardar nueva cuenta.
     * El mutator del modelo gestiona el cifrado de password_enc.
     */
    public function store(AccountStoreRequest $request)
    {
        $data = $request->validated();

        // Mapear password plano al campo cifrado si viene en el request
        if (!empty($data['password'])) {
            $data['password_enc'] = $data['password'];
            unset($data['password']);
        }

        Account::create($data);

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Cuenta creada con éxito.');
    }

    /**
     * Muestra el detalle y el historial de asignaciones de una cuenta.
     */
   public function show(Account $account)
    {
        $this->authorize('view', $account);

        // Cargamos la cuenta con su historial de asignaciones
        $account->load(['assignments' => function ($query) {
            $query->with('rider')
                  // --- ESTA LÍNEA ES LA MAGIA ---
                  // Ordena primero por el estado 'active' y luego por 'ended'
                  ->orderByRaw("FIELD(status, 'active', 'ended')")
                  // Después, ordena por la fecha de inicio más reciente
                  ->orderBy('start_at', 'desc');
        }]);

        return view('content.admin.accounts.show', compact('account'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Account $account)
    {
        return view('content.admin.accounts.edit', compact('account'));
    }

    /**
     * Actualizar cuenta existente.
     */
    public function update(AccountUpdateRequest $request, Account $account)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password_enc'] = $data['password'];
        }
        // Nunca persistimos 'password' en claro
        unset($data['password']);

        $account->update($data);

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Cuenta actualizada con éxito.');
    }

    /**
     * Eliminar cuenta.
     * Responde JSON si la petición es AJAX.
     */
    public function destroy(Request $request, Account $account)
    {
        $account->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cuenta eliminada con éxito.',
            ]);
        }

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Cuenta eliminada con éxito.');
    }

    /**
     * Helper para CSRF en botones renderizados como HTML.
     */
    private function csrfToken(): string
    {
        return csrf_token();
    }
}
