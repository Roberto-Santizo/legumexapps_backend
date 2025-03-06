<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionTimeout extends Model
{
    protected $fillable =[
        'timeout_id',
        'task_p_id'
    ];
}
