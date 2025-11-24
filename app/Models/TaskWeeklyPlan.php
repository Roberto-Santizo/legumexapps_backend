<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWeeklyPlan extends Model
{
    protected $fillable = [
        'weekly_plan_id',
        'tarea_id',
        'workers_quantity',
        'budget',
        'hours',
        'slots',
        'extraordinary',
        'start_date',
        'end_date',
        'operation_date',
        'plantation_control_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'operation_date' => 'datetime',
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
        return $this->belongsTo(PlantationControl::class,'plantation_control_id','id');
    }

    public function closures()
    {
        return $this->hasMany(PartialClosure::class, 'task_weekly_plan_id', 'id');
    }

    public function employees()
    {
        return $this->hasMany(EmployeeTask::class, 'task_weekly_plan_id', 'id');
    }
    public function insumos()
    {
        return $this->hasMany(TaskInsumos::class);
    }

    public function weeklyPlanChanges()
    {
        return $this->hasMany(BinnacleTaskWeeklyPlan::class);
    }
}
