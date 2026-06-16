<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgDisposeScan extends Model
{
    protected $fillable = [
        'label_id',
        'part_code',
        'part_name',
        'lot_no',
        'qty_box',
        'scanned_at',
        'operator_id',
        'created_by',
        'dispose_at',
        'dispose_operator_id',
        'remark',
    ];

    protected $casts = [
        'qty_box' => 'integer',
        'scanned_at' => 'datetime',
        'dispose_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function disposeOperator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'dispose_operator_id');
    }
}
