<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteChecklistCondition extends Model
{
    protected $fillable = [
        'lote_checklist_id',
        'crop_disease_syptom_id',
        'exists',
        'level',
        'observations'
    ];
}
