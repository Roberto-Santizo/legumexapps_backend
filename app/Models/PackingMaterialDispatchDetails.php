<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialDispatchDetails extends Model
{
    protected $fillable = [
        'pm_dispatch_id',
        'packing_material_id',
        'quantity',
        'lote',
    ];

    public function item()
    {
        return $this->belongsTo(PackingMaterial::class, 'packing_material_id', 'id');
    }

    public function dispatch()
    {
        return $this->belongsTo(PackingMaterialDispatch::class, 'pm_dispatch_id', 'id');
    }
}
