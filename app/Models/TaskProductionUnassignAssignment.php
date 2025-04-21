<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionUnassignAssignment extends Model
{
    protected $fillable = [
        'task_p_unassign_id',
        'assignment_id',
        'hours'
    ];

    public function taskProductionUnassign()
    {
        return $this->belongsTo(TaskProductionUnassign::class,'task_p_unassign_id','id');
    }
}
