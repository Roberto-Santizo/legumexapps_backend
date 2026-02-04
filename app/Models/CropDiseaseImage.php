<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropDiseaseImage extends Model
{
    protected $fillable = [
        'agricola_image_id',
        'crop_disease_id'
    ];

    public function image()
    {
        return $this->belongsTo(AgricolaImage::class, 'agricola_image_id', 'id');
    }
}
