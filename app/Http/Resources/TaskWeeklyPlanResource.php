<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskWeeklyPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => strval($this->id),
            'cdp' => $this->lotePlantationControl->cdp->name,
            'lote' => $this->lotePlantationControl->lote->name,
            'task' => $this->task->name,
            'week' => $this->plan->week,
            'finca_id' => strval($this->plan->finca->id),
            'weekly_plan_id' => strval($this->plan->id),
            'lote_plantation_control_id' => strval($this->lotePlantationControl->id),
            'hours' => $this->hours,
            'budget' => $this->budget,
            'minimum_slots' => ceil($this->hours/12),
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            "start_time" => $this->start_date ? $this->start_date->format('H:i') : null,
            "end_time" => $this->end_date ? $this->end_date->format('H:i') : null,
            'slots' => $this->slots,
            'active_closure' => $this->closures()->where('start_date','!=',null)->where('end_date',null)->count() > 0 ? true : false,
        ];
    }
}
