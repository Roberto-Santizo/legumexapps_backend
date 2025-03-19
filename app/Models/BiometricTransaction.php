<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricTransaction extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'acc_transaction';
}
