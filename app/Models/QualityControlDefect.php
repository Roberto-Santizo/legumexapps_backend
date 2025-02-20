<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlDefect extends Model
{
    protected $fillable = [
        'quality_control_doc_id',
        'defect_id',
        'input',
        'result',
        'tolerance_percentage'
    ];

    public function defect()
    {
        return $this->belongsTo(Defect::class, 'defect_id','id');
    }

}
