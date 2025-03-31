<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionPerformance extends Model
{
    protected $fillable = [
        'task_production_plan_id',
        'tarimas_produced',
        'lbs_bascula'
    ];
}
