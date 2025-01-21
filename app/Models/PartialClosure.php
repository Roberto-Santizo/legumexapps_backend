<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartialClosure extends Model
{
    protected $fillable = [
        'task_weekly_plan_id',
        'start_date',
        'end_date'
    ];
}
