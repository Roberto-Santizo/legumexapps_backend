<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnitsProduct extends Model
{
    protected $fillable = [
        'name',
        'presentation',
        'box_weight'
    ];
}
