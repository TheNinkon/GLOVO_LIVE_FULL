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

    /**
     * Get the schedules for the forecast.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Forecast $forecast) {
            // Delete all associated schedules before deleting the forecast
            $forecast->schedules()->delete();
        });
    }
}
