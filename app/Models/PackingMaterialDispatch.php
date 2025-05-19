<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialDispatch extends Model
{
    protected $fillable = [
        'task_production_plan_id',
        'user_id',
        'reference',
        'responsable_bags',
        'responsable_boxes',
        'signature_responsable_bags',
        'signature_responsable_boxes',
        'user_signature',
        'observations',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(TaskProductionPlan::class,'task_production_plan_id','id');
    }

    public function items()
    {
        return $this->hasMany(PackingMaterialDispatchDetails::class, 'pm_dispatch_id', 'id');
    }
}
