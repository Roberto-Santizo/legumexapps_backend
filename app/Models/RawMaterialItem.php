<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialItem extends Model
{
    protected $fillable = [
        'code',
        'product_name',
        'type'
    ];
}
