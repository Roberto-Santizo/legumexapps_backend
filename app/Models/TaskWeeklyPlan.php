<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWeeklyPlan extends Model
{
    protected $fillable =[
        'weekly_plan_id',
        'lote_plantation_control_id',
        'tarea_id',
        'workers_quantity',
        'budget',
        'hours',
        'slots',
        'extraordinary'
    ];
}
