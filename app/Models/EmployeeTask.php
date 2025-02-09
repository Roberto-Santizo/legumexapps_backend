<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    protected $fillable = [
        'name',
        'code',
        'task_weekly_plan_id',
        'employee_id',
    ];

    public function task_weekly_plan()
    {
        return $this->belongsTo(TaskWeeklyPlan::class);
    }

}
