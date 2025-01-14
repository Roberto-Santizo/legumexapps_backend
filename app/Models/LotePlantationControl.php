<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotePlantationControl extends Model
{
    
    protected $fillable = [
        'lote_id',
        'plantation_controls_id',
        'status'
    ];

    public function cdp()
    {
        return $this->belongsTo(PlantationControl::class,'plantation_controls_id','id');
    }
}
