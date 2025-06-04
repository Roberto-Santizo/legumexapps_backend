<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnitRecipe extends Model
{
    protected $fillable = [
        'item_id',
        'sku_id',
        'lbs_per_item'
    ];

    public function item()
    {
        return $this->belongsTo(PackingMaterial::class,'item_id','id');
    }
}
