<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportInspectionCondition extends Model
{
    protected $fillable = [
        'transport_condition_id',
        'transport_inspection_id',
        'status',
    ];


    public function condition()
    {
        return $this->belongsTo(TransportCondition::class,'transport_condition_id','id');
    }
}
