<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmReception extends Model
{
    protected $fillable = [
        'grn',
        'doc_date',
        'finca_id'
    ];


    public function field_data()
    {
        return $this->hasOne(FieldDataReception::class);
    }

    public function prod_data()
    {
        return $this->hasOne(ProdDataReception::class);
    }

    public function quality_control_doc_data()
    {
        return $this->hasOne(QualityControlDoc::class);
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }
}
