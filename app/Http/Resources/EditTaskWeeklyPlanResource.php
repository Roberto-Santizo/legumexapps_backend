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
            'budget' => $this->budget,
            'slots' => $this->workers_quantity,
            'weekly_plan_id' => strval($this->weekly_plan_id),
            'hours' => $this->hours,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'end_time' => $this->end_date ? $this->end_date->format('H:i:s') : null,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'start_time' => $this->start_date ? $this->start_date->format('H:i:s') : null,
        ];
    }
}
