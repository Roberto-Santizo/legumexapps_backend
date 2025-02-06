<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAssignments extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected $fillable = [
        'task_crop_weekly_plan_id',
        'start_date',
        'end_date'
    ];

    public function employees()
    {
        return $this->hasMany(EmployeeTaskCrop::class,'daily_assignment_id','id');
    }
}
