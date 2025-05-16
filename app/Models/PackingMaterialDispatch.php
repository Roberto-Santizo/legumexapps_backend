<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialDispatch extends Model
{
    protected $fillable = [
        "observations",
        "received_by_boxes",
        "received_by_signature_boxes",
        "received_by_bags",
        "received_by_signature_bags",
        "user_signature",
        "task_production_plan_id",
        "reference",
        "quantity_boxes",
        "quantity_bags",
        "quantity_inner_bags",
        "user_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(TaskProductionPlan::class,'task_production_plan_id','id');
    }
}
