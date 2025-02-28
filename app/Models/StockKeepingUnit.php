<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    protected $fillable =[
        'name',
        'code',
        'unit_mesurment'
    ];
}
