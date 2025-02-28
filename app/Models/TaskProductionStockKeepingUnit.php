<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionStockKeepingUnit extends Model
{
    protected $fillable = [
        'task_p_id',
        'sku_id',
        'start_date',
        'end_date',
        'tarimas',
    ];

    public function closures()
    {
        return $this->hasMany(PartialProductionClosure::class,'task_p_sku_id','id');
    }

    public function task_production_plan()
    {
        return $this->belongsTo(TaskProductionPlan::class,'task_p_id','id');
    }

    public function sku()
    {
        return $this->belongsTo(StockKeepingUnit::class,'sku_id','id');
    }
}
