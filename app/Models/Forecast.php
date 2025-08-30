<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'week_start_date',
        'forecast_data',
        'booking_deadline',
    ];

    protected $casts = [
        'forecast_data' => 'array',
        'week_start_date' => 'date',
        'booking_deadline' => 'datetime',
    ];

    // Relación con los horarios
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
