<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionUnassign extends Model
{
    protected $fillable = [
        'task_p_id',
        'user_id',
        'reason',
        'employee_signature',
        'supervisor_signature',
        'auditor_signature',
        'detail',
    ];
}
