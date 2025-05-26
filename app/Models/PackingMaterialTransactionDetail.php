<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialTransactionDetail extends Model
{
    protected $fillable = [
        'pm_transaction_id',
        'packing_material_id',
        'quantity',
        'lote',
        'destination'
    ];

    public function transaction()
    {
        return $this->belongsTo(PackingMaterialTransaction::class, 'pm_transaction_id', 'id');
    }

    public function item()
    {
        return $this->hasOne(PackingMaterial::class,'id','packing_material_id');
    }
}
