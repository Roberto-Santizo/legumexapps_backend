<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOperationChange extends Model
{
    protected $fillable = [
        'user_id',
        'task_production_plan_id',
        'notified_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(TaskProductionPlan::class,'task_production_plan_id','id');
    }
}
