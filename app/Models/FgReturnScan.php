<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgReturnScan extends Model
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
        'return_at',
        'return_operator_id',
        'remark',
    ];

    protected $casts = [
        'qty_box' => 'integer',
        'scanned_at' => 'datetime',
        'return_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function returnOperator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'return_operator_id');
    }
}
