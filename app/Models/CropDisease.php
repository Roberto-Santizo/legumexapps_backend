<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropDisease extends Model
{
    protected $fillable = [
        'name',
        'crop_id'
    ];
}
