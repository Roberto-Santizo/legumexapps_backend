<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropPart extends Model
{
    protected $fillable = [
        'name',
        'crop_id'
    ];

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
