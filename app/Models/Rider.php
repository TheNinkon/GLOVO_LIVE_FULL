<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rider extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Guard asociado al modelo.
     * @var string
     */
    protected $guard = 'rider';

    protected $fillable = [
        'full_name',
        'dni',
        'city',
        'phone',
        'email',
        'password',
        'start_date',
        'status',
        'notes',
        'weekly_contract_hours',
        'edits_remaining',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'start_date' => 'date',
        ];
    }

    /** Historial de asignaciones del rider */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /** Asignación activa (si existe) */
    public function activeAssignment(): HasOne
    {
        return $this->hasOne(Assignment::class)->where('status', 'active');
    }

    /**
     * Define la relación: Un Rider tiene muchos Schedules (horas reservadas).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Define la relación: Un Rider tiene muchas asignaciones de prefactura.
     */
    public function prefacturaAssignments(): HasMany
    {
        return $this->hasMany(PrefacturaAssignment::class);
    }
}
