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

    public function symptom()
    {
        return $this->belongsTo(CropDiseaseSyptom::class, 'crop_disease_syptom_id', 'id');
    }

    public function checklist()
    {
        return $this->belongsTo(LoteChecklist::class, 'lote_checklist_id', 'id');
    }
}
