<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id',
        'forecast_id',
        'slot_date',
        'slot_time',
        'is_submitted', // Nuevo campo
    ];

    protected $casts = [
        'slot_date' => 'date',
        'slot_time' => 'datetime:H:i:s',
        'is_submitted' => 'boolean', // Cast para asegurar que es un booleano
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class);
    }
}
