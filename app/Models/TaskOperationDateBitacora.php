<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskOperationDateBitacora extends Model
{
    protected $casts = [
        'original_date' => 'datetime',
        'new_date' => 'datetime',
    ];
    
    protected $fillable = [
        'task_production_plan_id',
        'user_id',
        'original_date',
        'new_date',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
