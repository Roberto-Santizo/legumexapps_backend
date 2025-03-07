<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyProductionPlan extends Model
{
    protected $fillable = [
        'week',
        'year'
    ];

    public function tasks()
    {
        return $this->hasMany(TaskProductionPlan::class);
    }
}
