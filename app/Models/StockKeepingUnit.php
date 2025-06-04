<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    protected $fillable = [
        'code',
        'product_name',
        'presentation',
        'boxes_pallet',
        'pallets_container',
        'hours_container',
        'client_name',
    ];

    public function product()
    {
        return $this->belongsTo(StockKeepingUnitsProduct::class);
    }

    public function items()
    {
        return $this->hasMany(StockKeepingUnitRecipe::class,'sku_id','id');
    }
}
