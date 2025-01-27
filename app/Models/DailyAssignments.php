<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAssignments extends Model
{
    protected $fillable = [
        'task_crop_weekly_plan_id',
        'start_date',
        'end_date'
    ];
}
