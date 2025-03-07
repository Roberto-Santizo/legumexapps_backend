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
}
