<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function variety()
    {
        return $this->belongsTo(VarietyProduct::class,'variety_product_id','id');
    }
}
