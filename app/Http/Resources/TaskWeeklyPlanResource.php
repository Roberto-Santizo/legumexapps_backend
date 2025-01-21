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
            'task' => $this->task->name,
            'week' => $this->plan->week,
            'hours' => $this->hours,
            'budget' => $this->budget,
            'start_date' => $this->start_date ? $this->start_date->format('d-m-Y h:i:s A') : null,
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y h:i:s A') : null,
            'active_closure' => $this->closures()->where('start_date','!=',null)->where('end_date',null)->count() > 0 ? true : false,
        ];
    }
}
