<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Rider extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'rider';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'dni',
        'city',
        'address',
        'birth_date',
        'status',
        'start_date',
        'end_date',
        'weekly_contract_hours',
        'edits_remaining',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the prefactura assignments for the rider.
     */
    public function prefacturaAssignments(): HasMany
    {
        return $this->hasMany(PrefacturaAssignment::class);
    }

    /**
     * Obtiene las métricas de Glovo para el rider a través de sus asignaciones.
     * La relación es indirecta: Rider -> Assignments -> Accounts -> Metrics.
     */
    public function glovoMetrics()
    {
        // Obtiene los courier_ids de las cuentas asignadas a este rider
        $courierIds = $this->assignments()
            ->with('account')
            ->get()
            ->pluck('account.courier_id');

        // Retorna las métricas que coincidan con esos courier_ids
        return Metric::whereIn('courier_id', $courierIds);
    }

    /**
     * Define la relación con las asignaciones (Assignments).
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
