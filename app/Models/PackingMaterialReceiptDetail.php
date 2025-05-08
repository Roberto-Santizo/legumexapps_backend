<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialReceiptDetail extends Model
{
    protected $fillable = [
        'p_material_id',
        'pm_receipt_id',
        'lote',
        'quantity'
    ];

    public function item()
    {
        return $this->hasOne(PackingMaterial::class,'id','p_material_id');
    }
}
