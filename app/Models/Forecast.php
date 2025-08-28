<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'week_start_date',
        'forecast_data',
        'booking_deadline', // <-- AÑADIR ESTA LÍNEA
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'forecast_data' => AsArrayObject::class, // Trata el JSON como un objeto
        'booking_deadline' => 'datetime', // <-- AÑADIR ESTA LÍNEA
    ];
}
