<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'code',
        'total_persons'
    ];

    public function tasks()
    {
        return $this->hasMany(TaskProductionPlan::class,'line_id','id');
    }
}
