<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    protected $fillable =[
        'code',
        'product_name',
        'presentation',
        'boxes_pallet',
        'config_box',
        'config_bag',
        'config_inner_bag',
        'pallets_container',
        'hours_container',
        'client_name'
    ];

    public function product()
    {
        return $this->belongsTo(StockKeepingUnitsProduct::class);
    }
}
