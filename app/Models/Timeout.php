<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeout extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'=> 'datetime'
    ];
    
    protected $fillable = [
        'name',
        'hours'
    ];
}
