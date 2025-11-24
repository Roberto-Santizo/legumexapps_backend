<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWeeklyPlanDraft extends Model
{
    protected $fillable = [
        'task_guideline_id',
        'draft_weekly_plan_id',
        'plantation_control_id',
        'hours',
        'budget',
        'slots',
        'tags'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function taskGuide()
    {
        return $this->hasOne(TaskGuideline::class, 'id', 'task_guideline_id');
    }

    public function cdp()
    {
        return $this->hasOne(PlantationControl::class, 'id', 'plantation_control_id');
    }
}
