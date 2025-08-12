<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmReception extends Model
{
    protected $casts = [
        'doc_date' => 'datetime'
    ];

    protected $fillable = [
        'grn',
        'doc_date',
        'finca_id',
        'consignacion',
        'quality_status_id',
        'user_id'
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

    public function transport_doc_data()
    {
        return $this->hasOne(TransportInspectionRmReception::class,'reception_id','id');
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function status()
    {
        return $this->belongsTo(QualityStatus::class,'quality_status_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
