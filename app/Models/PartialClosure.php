<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartialClosure extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'=> 'datetime'
    ];

    protected $fillable = [
        'task_weekly_plan_id',
        'start_date',
        'end_date'
    ];

    public function taskWeeklyPlan(){
        return $this->belongsTo(TaskWeeklyPlan::class);
    }
}
