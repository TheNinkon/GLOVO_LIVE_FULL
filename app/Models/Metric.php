<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metric extends Model
{
    use HasFactory;

    protected $table = 'glovo_metrics';

    protected $fillable = [
        'courier_id',
        'transport',
        'fecha',
        'ciudad',
        'pedidos_entregados',
        'cancelados',
        'reasignaciones',
        'no_show',
        'horas',
        'ratio_entrega',
        'tiempo_promedio',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Define la relación: Una métrica de Glovo pertenece a un rider.
     */
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'courier_id', 'id');
    }
}
