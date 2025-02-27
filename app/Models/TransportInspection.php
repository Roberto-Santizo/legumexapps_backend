<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportInspection extends Model
{
    protected $casts =[
        'date' => 'datetime'
    ];
    protected $fillable = [
        'planta_id',
        'product_id',
        'rm_reception_id',
        'pilot_name',
        'truck_type',
        'plate',
        'date',
        'observations'
    ];

    public function rm_reception()
    {
        return $this->belongsTo(RmReception::class);
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function conditions()
    {
        return $this->hasMany(TransportInspectionCondition::class);
    }
}
