<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable =[
        'name',
        'dpi',
        'license',
        'carrier_id'
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
