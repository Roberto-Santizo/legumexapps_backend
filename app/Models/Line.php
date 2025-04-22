<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'code',
        'shift',
        'name'
    ];

    public function tasks()
    {
        return $this->hasMany(TaskProductionPlan::class,'line_id','id');
    }

    public function skus()
    {
        return $this->hasMany(LineStockKeepingUnits::class);
    }

    public function positions()
    {
        return $this->hasMany(LinePosition::class);
    }
}
