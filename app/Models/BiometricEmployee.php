<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricEmployee extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'pers_person';
}
