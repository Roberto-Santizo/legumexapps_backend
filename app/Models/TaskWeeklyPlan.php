<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
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
        'extraordinary',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function task()
    {
        return $this->belongsTo(Tarea::class,'tarea_id','id');
    }

    public function plan()
    {
        return $this->belongsTo(WeeklyPlan::class,'weekly_plan_id','id');
    }

    public function lotePlantationControl()
    {
        return $this->belongsTo(LotePlantationControl::class,'lote_plantation_control_id','id');
    }
}
