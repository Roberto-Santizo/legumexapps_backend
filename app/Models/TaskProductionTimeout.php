<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductionTimeout extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    protected $fillable =[
        'timeout_id',
        'task_p_id',
        'start_date',
        'end_date'
    ];

    public function timeout()
    {
        return $this->belongsTo(Timeout::class);
    }
}
