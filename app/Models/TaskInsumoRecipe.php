<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskInsumoRecipe extends Model
{
    protected $fillable = [
        "task_guideline_id",
        "insumo_id",
        "quantity"
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
