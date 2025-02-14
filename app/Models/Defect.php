<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    protected $fillable = [
        'name',
        'tolerance_percentage',
        'quality_variety_id'
    ];

    public function quality_variety()
    {
        return $this->belongsTo(QualityVariety::class);
    }
    
}
