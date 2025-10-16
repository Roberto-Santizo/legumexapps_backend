<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskGuideline extends Model
{
    protected $fillable = [
        "task_id",
        "recipe_id",
        "crop_id",
        "finca_id",
        "week",
        "budget",
        "hours",
    ];

    public function task()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
