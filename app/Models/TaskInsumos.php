<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskInsumos extends Model
{
    protected $fillable = [
        'insumo_id',
        'task_weekly_plan_id',
        'assigned_quantity',
        'used_quantity'
    ];

    public function insumo() 
    {
        return $this->belongsTo(Insumo::class);
    }
}
