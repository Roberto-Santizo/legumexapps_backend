<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductorPlantationControl extends Model
{
    protected $fillable = [
        'name',
        'finca_id',
        'status'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }
}
