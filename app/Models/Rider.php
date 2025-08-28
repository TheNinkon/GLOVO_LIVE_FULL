<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Get the Glovo metrics for the rider.
     */
    public function glovoMetrics(): HasMany
    {
        return $this->hasMany(Metric::class, 'courier_id', 'courier_id');
    }
}
