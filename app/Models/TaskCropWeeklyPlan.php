<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaskCropWeeklyPlan extends Model
{
    protected $fillable = [
        'weekly_plan_id',
        'plantation_control_id',
        'tarea_id'
    ];

    public function task()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(WeeklyPlan::class, 'weekly_plan_id', 'id');
    }

    public function cdp()
    {
        return $this->belongsTo(PlantationControl::class, 'plantation_control_id', 'id');
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
