<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftWeeklyProductionPlan extends Model
{
    protected $fillable = [
        'week',
        'year',
        'production_confirmation',
        'bodega_confirmation',
        'logistics_confirmation',
        'confirmation_date'
    ];

    protected $casts = [
        'confirmation_date' => 'datetime'
    ];

    public function tasks()
    {
        return $this->hasMany(TaskProductionDraft::class, 'draft_weekly_production_plan_id');
    }
}
