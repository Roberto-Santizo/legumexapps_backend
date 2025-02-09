<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinishedTaskCropResource extends JsonResource
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
            'task' => $this->TaskCropWeeklyPlan->task->name,
            'finca' => $this->TaskCropWeeklyPlan->plan->finca->name,
            'lote' => $this->TaskCropWeeklyPlan->lotePlantationControl->lote->name,
            'start_date' => $this->start_date->format('d-m-Y h:i:s A'),
            'end_date' => $this->end_date->format('d-m-Y h:i:s A'),

        ];
    }
}
