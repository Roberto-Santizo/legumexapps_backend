<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{
     public function cdps()
    {
        return $this->hasMany(ProductorPlantationControl::class);
    }
}
