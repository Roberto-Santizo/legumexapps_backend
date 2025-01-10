<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $fillable = [
        'name',
        'finca_id'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function cdp()
    {
        return $this->hasMany(LotePlantationControl::class)->where('status',1);
    }
}
