<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $fillable =[
        'code',
        'name'
    ];

    public function plates()
    {
        return $this->hasMany(Plate::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
