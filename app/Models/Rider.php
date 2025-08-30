<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rider extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'rider';

    protected $fillable = [
        'name',
        'email',
        'password',
        'city', // Añadimos la columna de ciudad
        'weekly_contract_hours',
        'edits_remaining',
        'schedule_is_locked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'schedule_is_locked' => 'boolean',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'assignments')->withTimestamps();
    }
}
