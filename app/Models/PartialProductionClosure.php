<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartialProductionClosure extends Model
{
    protected $fillable = [
        'task_p_sku_id',
        'start_date',
        'end_date'
    ];
}
