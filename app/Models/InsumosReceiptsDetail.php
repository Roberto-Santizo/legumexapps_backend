<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumosReceiptsDetail extends Model
{
    protected $fillable =[
        'insumo_id',
        'insumos_receipt_id',
        'units',
        'total'
    ];

    public function insumo()
    {
        return $this->hasOne(Insumo::class,'id','insumo_id');
    }
}
