<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrefacturaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefactura_id',
        'courier_id',
        'cash_out',
        'tips',
    ];

    public function prefactura(): BelongsTo
    {
        return $this->belongsTo(Prefactura::class);
    }

    /**
     * Un PrefacturaItem tiene muchas asignaciones.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PrefacturaAssignment::class);
    }
}
