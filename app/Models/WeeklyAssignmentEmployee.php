<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyAssignmentEmployee extends Model
{
    protected $fillable = [
        'lote_id',
        'code',
        'name',
        'weekly_plan_id'
    ];
}
