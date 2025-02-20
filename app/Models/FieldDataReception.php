<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldDataReception extends Model
{
    protected $fillable = [
        'producer_id',
        'rm_reception_id',
        'product_id',
        'transport',
        'pilot_name',
        'inspector_name',
        'cdp',
        'transport_plate',
        'weight',
        'total_baskets',
        'weight_baskets',
        'basket_id',
        'quality_percentage',
        'inspector_signature',
        'prod_signature',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function basket()
    {
        return $this->belongsTo(Basket::class);
    }

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }
    
}
