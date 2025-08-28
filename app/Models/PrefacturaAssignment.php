<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrefacturaAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefactura_item_id',
        'rider_id',
        'amount',
        'type',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PrefacturaItem::class, 'prefactura_item_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
