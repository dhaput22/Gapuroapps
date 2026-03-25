<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgReceivingScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'label_id',
        'part_code',
        'part_name',
        'lot_no',
        'qty_box',
        'scanned_at',
        'created_by',
        'operator_id',
    ];

    protected $casts = [
        'qty_box' => 'integer',
        'scanned_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
