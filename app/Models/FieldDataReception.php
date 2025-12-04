<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldDataReception extends Model
{
    protected $fillable = [
        'producer_id',
        'rm_reception_id',
        'product_id',
        'inspector_name',
        'weight',
        'total_baskets',
        'weight_baskets',
        'basket_id',
        'quality_percentage',
        'inspector_signature',
        'prod_signature',
        'driver_signature',
        'plate_id',
        'carrier_id',
        'cdp_id',
        'pilot_name'
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

    public function plate()
    {
        return $this->belongsTo(Plate::class);
    }

    public function cdp()
    {
        return $this->belongsTo(ProductorPlantationControl::class, 'cdp_id', 'id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
