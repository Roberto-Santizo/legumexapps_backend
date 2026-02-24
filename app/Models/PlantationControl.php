<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantationControl extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'lote_id',
        'total_plants',
        'recipe_id',
        'crop_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function draftTasks()
    {
        return $this->hasMany(TaskWeeklyPlanDraft::class);
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
