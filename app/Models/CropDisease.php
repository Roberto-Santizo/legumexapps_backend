<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropDisease extends Model
{
    protected $fillable = [
        'name',
        'crop_id',
        'week'
    ];

    public function images()
    {
        return $this->hasMany(CropDiseaseImage::class);
    }

    public function symptoms()
    {
        return $this->hasMany(CropDiseaseSyptom::class);
    }
}
