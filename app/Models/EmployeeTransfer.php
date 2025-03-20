<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTransfer extends Model
{
    protected $fillable =[
        'change_employee_id',
        'permission',
        'confirmed'
    ];


    public function bitacora()
    {
        return $this->belongsTo(TaskProductionEmployeesBitacora::class, 'change_employee_id','id');
    }
}
