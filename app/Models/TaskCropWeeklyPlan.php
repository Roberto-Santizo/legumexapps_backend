<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaskCropWeeklyPlan extends Model
{
    protected $fillable = [
        'weekly_plan_id',
        'lote_plantation_control_id',
        'task_crop_id'
    ];

    public function task()
    {
        return $this->belongsTo(TaskCrop::class, 'task_crop_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(WeeklyPlan::class, 'weekly_plan_id', 'id');
    }

    public function lotePlantationControl()
    {
        return $this->belongsTo(LotePlantationControl::class, 'lote_plantation_control_id', 'id');
    }


    public function assignment_today()
    {
        return $this->hasOne(DailyAssignments::class)->whereDate('start_date', Carbon::now());
    }

    public function assigments()
    {
        return $this->hasMany(DailyAssignments::class);
    }

    public function employees()
    {
        return $this->hasMany(EmployeeTaskCrop::class);
    }
}
