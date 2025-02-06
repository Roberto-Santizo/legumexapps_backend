<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'iclock_transaction';

    protected $casts = [
        'punch_time' => 'datetime'
    ];

    public function empleado()
    {
        return $this->hasOne(PersonnelEmployee::class,'id','emp_id');
    }
}
