<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    protected $fillable =[
        'code',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(StockKeepingUnitsProduct::class);
    }
}
