<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialSkuRecipe extends Model
{
    protected $fillable = [
        'stock_keeping_unit_id',
        'raw_material_item_id',
        'percentage'
    ];

    public function sku()
    {
        return $this->belongsTo(StockKeepingUnit::class);
    }

    public function item()
    {
        return $this->hasOne(RawMaterialItem::class,'id','raw_material_item_id');
    }
}
