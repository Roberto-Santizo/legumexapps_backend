<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftWeeklyPlan extends Model
{
    protected $fillable = [
        'week',
        'year',
        'finca_id'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function tasks()
    {
        return $this->hasMany(TaskWeeklyPlanDraft::class);
    }
}
