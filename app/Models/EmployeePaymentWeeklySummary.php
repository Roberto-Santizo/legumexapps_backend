<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePaymentWeeklySummary extends Model
{
    protected $fillable = [
        'code',
        'name',
        'emp_id',
        'hours',
        'amount',
        'task_weekly_plan_id',
        'daily_assignment_id',
        'weekly_plan_id',
        'date',
        'theorical_hours'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function plan()
    {
        return $this->belongsTo(WeeklyPlan::class, 'weekly_plan_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(TaskWeeklyPlan::class, 'task_weekly_plan_id', 'id');
    }

    public function assigment()
    {
        return $this->belongsTo(DailyAssignments::class, 'daily_assignment_id', 'id');
    }
}
