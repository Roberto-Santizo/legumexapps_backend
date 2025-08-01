<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionDraft extends Model
{
    protected $fillable = [
        'draft_weekly_production_plan_id',
        'line_id',
        'stock_keeping_unit_id',
        'total_lbs',
        'destination'
    ];

    public function draftWeeklyProductionPlan()
    {
        return $this->belongsTo(DraftWeeklyProductionPlan::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function sku()
    {
        return $this->belongsTo(StockKeepingUnit::class, 'stock_keeping_unit_id', 'id');
    }

    public function line_performance()
    {
        return $this->hasOne(LineStockKeepingUnits::class, 'sku_id', 'stock_keeping_unit_id')->whereColumn('line_id', 'line_id');
    }
}
