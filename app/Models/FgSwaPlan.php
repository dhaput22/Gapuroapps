<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FgSwaPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_code',
        'part_name',
        'start_lot_no',
        'end_lot_no',
        'qty_box',
        'total_plan',
        'created_by',
    ];

    protected $casts = [
        'qty_box' => 'integer',
        'total_plan' => 'integer',
    ];
}
