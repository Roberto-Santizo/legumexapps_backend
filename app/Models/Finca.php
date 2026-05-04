<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{

    protected $fillable = [
        'name',
        'code',
        'terminal_id'
    ];

    public function cdps()
    {
        return $this->hasMany(ProductorPlantationControl::class);
    }
}
