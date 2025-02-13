<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdDataReception extends Model
{
    protected $fillable = [
        'rm_reception_id',
        'total_baskets',
        'weight_baskets',
        'gross_weight',
        'net_weight',
        'inspector_signature'
    ];
}
