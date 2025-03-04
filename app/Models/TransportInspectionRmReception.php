<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportInspectionRmReception extends Model
{
    protected $fillable = [
        'transport_id',
        'reception_id'
    ];

    protected function transport_data()
    {
        return $this->belongsTo(TransportInspection::class,'transport_id','id');
    }

    protected function reception_data()
    {
        return $this->belongsTo(RmReception::class,'reception_id','id');
    }

}
