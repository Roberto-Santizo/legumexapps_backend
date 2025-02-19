<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'variety_product_id',
        'accepted_percentage'
    ];
    public function variety()
    {
        return $this->belongsTo(VarietyProduct::class,'variety_product_id','id');
    }

    public function defects()
    {
        return $this->hasMany(Defect::class);
    }
}
