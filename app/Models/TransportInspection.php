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
        'pilot_name',
        'truck_type',
        'plate',
        'date',
        'observations',
        'verify_by_signature',
        'quality_manager_signature',
        'user_id'
    ];

    public function rm_reception()
    {
        return $this->belongsTo(TransportInspectionRmReception::class,'id','transport_id');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
