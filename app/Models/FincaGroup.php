<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FincaGroup extends Model
{
    protected $fillable = [
        'lote_id',
        'code',
        'finca_id'
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }
}
