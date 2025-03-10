<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricDepartment extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'auth_department';

    protected $casts = [
        'id' => 'string'
    ];
}
