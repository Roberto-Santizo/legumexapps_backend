<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionPlanNote extends Model
{
    protected $fillable =[
        'task_p_id',
        'reason',
        'action' ,
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
