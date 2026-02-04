<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropDiseaseSyptom extends Model
{
    protected $fillable = [
        'symptom',
        'crop_disease_id',
        'crop_part_id'
    ];

    public function disease()
    {
        return $this->belongsTo(CropDisease::class);
    }

    public function cropPart()
    {
        return $this->belongsTo(CropPart::class);
    }
}
