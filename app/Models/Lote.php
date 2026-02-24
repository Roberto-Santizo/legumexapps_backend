<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $fillable = [
        'name',
        'finca_id',
        'size',
        'total_plants'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function cdp()
    {
        return $this->hasMany(PlantationControl::class, 'lote_id', 'id');
    }

    public function lote_cdps()
    {
        return $this->hasMany(LotePlantationControl::class, 'lote_id', 'id');
    }
}
