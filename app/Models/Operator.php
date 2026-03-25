<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'department',
    ];

    public function receivingScans(): HasMany
    {
        return $this->hasMany(FgReceivingScan::class);
    }
}

