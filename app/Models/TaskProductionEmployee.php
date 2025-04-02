<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionEmployee extends Model
{
    protected $fillable = [
        'task_p_id',
        'name',
        'code',
        'position'
    ];


    public function TaskProduction()
    {
        return $this->belongsTo(TaskProductionPlan::class,'task_p_id','id');
    }

    public function bitacoras()
    {
        return $this->hasMany(TaskProductionEmployeesBitacora::class,'assignment_id','id');
    }
}
