<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinishedTasksWeeklyPlanResource extends JsonResource
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
            'task' => $this->task->name,
            'finca' => $this->plan->finca->name,
            'lote' => $this->lotePlantationControl->lote->name,
            'start_date' => '',
            'end_date' => '',
        ];
    }
}
