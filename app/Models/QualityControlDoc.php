<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlDoc extends Model
{

    protected $casts = [
        'doc_date' => 'datetime',
    ];
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

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function defects()
    {
        return $this->hasMany(QualityControlDefect::class);
    }
}
