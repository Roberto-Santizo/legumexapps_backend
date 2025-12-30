<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditTaskWeeklyPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'weekly_plan_id' => $this->weekly_plan_id,
            'group_id' => $this->finca_group_id,
            'budget' => $this->budget,
            'hours' => $this->hours,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'start_time' => $this->start_date ? $this->start_date->format('H:m') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'end_time' => $this->end_date ? $this->end_date->format('H:m') : null,
            'operation_date' => $this->operation_date ? $this->operation_date->format('Y-m-d') : null
        ];
    }
}
