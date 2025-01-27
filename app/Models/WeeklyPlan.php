<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPlan extends Model
{

    protected $fillable = [
        'finca_id',
        'year',
        'week'
    ];
    
    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function tasks() 
    {
        return  $this->hasMany(TaskWeeklyPlan::class);
    }

    public function tasks_crops() 
    {
        return  $this->hasMany(TaskCropWeeklyPlan::class);
    }
}
