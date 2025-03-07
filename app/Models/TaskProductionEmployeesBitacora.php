<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionEmployeesBitacora extends Model
{
    protected $fillable = [
        "assignment_id",
        "original_name",
        "original_code",
        "original_position",
        "new_name",
        "new_code",
        "new_position",
    ];
}
