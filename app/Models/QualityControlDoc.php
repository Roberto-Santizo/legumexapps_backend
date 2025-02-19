<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlDoc extends Model
{
    protected $fillable = [
        'rm_reception_id',
        'producer_id',
        'net_weight',
        'no_doc_cosechero',
        'sample_units',
        'total_baskets',
        'ph',
        'brix',
        'percentage',
        'valid_pounds',
        'user_id',
        'doc_date',
        'observations',
        'inspector_signature'
    ];
}
