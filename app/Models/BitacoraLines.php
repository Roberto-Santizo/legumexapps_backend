<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraLines extends Model
{
    protected $fillable =[
        'line_id',
        'old_code',
        'new_code',
        'old_total_persons',
        'new_total_persons',
    ];
}
