<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyAssignmentEmployee extends Model
{
    protected $fillable = [
        'lote_id',
        'code',
        'name',
        'weekly_plan_id',
        'finca_group_id'
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function group()
    {
        return $this->belongsTo(FincaGroup::class, 'finca_group_id', 'id');
    }
}
