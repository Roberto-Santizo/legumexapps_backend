<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialWastage extends Model
{
    protected $fillable = [
        'task_p_id',
        'packing_material_id',
        'quantity',
        'lote'
    ];

    public function item()
    {
        return $this->belongsTo(PackingMaterial::class,'packing_material_id','id');
    }
}
