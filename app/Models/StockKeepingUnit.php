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
        'config_box',
        'config_bag',
        'config_inner_bag',
        'pallets_container',
        'hours_container',
        'client_name',
        'box_id',
        'bag_id',
        'bag_inner_id'
    ];

    public function product()
    {
        return $this->belongsTo(StockKeepingUnitsProduct::class);
    }

    public function box()
    {
        return $this->hasOne(PackingMaterial::class,'id','box_id');
    }

     public function bag()
    {
        return $this->hasOne(PackingMaterial::class,'id','bag_id');
    }

     public function bag_inner()
    {
        return $this->hasOne(PackingMaterial::class,'id','bag_inner_id');
    }
}
