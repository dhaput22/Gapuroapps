<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgDeliveryScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'label_id',
        'part_code',
        'part_name',
        'lot_no',
        'qty_box',
        'scanned_at',
        'operator_id',
        'created_by',
        'delivery_at',
        'delivery_operator_id',
        'transfer_card_no',
    ];

    protected $casts = [
        'qty_box' => 'integer',
        'scanned_at' => 'datetime',
        'delivery_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function deliveryOperator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'delivery_operator_id');
    }
}
