<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    public function variety()
    {
        return $this->belongsTo(Variety::class);
    }
}
