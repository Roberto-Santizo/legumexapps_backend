<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinnacleTaskWeeklyPlan extends Model
{
    protected $fillable = [
        'task_weekly_plan_id',
        'from_plan',
        'to_plan'
    ];

    public function WeeklyPlanOrigin() 
    {
        return $this->belongsTo(WeeklyPlan::class,'from_plan','id');
    } 
}
