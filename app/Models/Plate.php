<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    protected $fillable =[
        'name',
        'carrier_id'
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
