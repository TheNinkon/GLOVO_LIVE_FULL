<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Assignment;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva asignación para una cuenta específica.
     */
    public function create(Account $account)
    {
        // Buscamos solo riders que no tengan una asignación activa actualmente
        $availableRiders = Rider::whereDoesntHave('assignments', function ($query) {
            $query->where('status', 'active');
        })->where('status', 'active')->pluck('full_name', 'id');

        return view('content.admin.assignments.create', compact('account', 'availableRiders'));
    }

    /**
     * Almacena la nueva asignación en la base de datos.
     */
    public function store(Request $request, Account $account)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'start_at' => 'required|date',
        ]);

        // Verificación de seguridad: una cuenta activa no puede ser asignada
        if ($account->assignments()->where('status', 'active')->exists()) {
            return redirect()->route('admin.accounts.index')->with('error', 'Esta cuenta ya tiene una asignación activa.');
        }

        Assignment::create([
            'account_id' => $account->id,
            'rider_id' => $request->rider_id,
            'start_at' => Carbon::parse($request->start_at),
            'status' => 'active',
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Cuenta asignada correctamente.');
    }

    /**
     * Finaliza una asignación activa.
     */
    public function end(Assignment $assignment)
    {
        $assignment->update([
            'status' => 'ended',
            'end_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Asignación finalizada correctamente.');
    }
}
