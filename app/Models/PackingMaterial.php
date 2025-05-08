<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterial extends Model
{
    protected $fillable =[
        'name',
        'description',
        'code',
        'blocked',
        'supplier_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(SupplierPackingMaterial::class);
    }
}
