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

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function tasks()
    {
        return $this->hasMany(TaskWeeklyPlan::class,'lote_plantation_control_id','id');
    }
}
