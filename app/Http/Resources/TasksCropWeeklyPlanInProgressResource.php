<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksCropWeeklyPlanInProgressResource extends JsonResource
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
            'lote' => $this->TaskCropWeeklyPlan->lotePlantationControl->lote->name,
            'finca' => $this->TaskCropWeeklyPlan->plan->finca->name,
            'week' => $this->TaskCropWeeklyPlan->plan->week,
            'assigned_employees' => $this->employees->count(),
            'total_employees' => null,
            'paused' => false
        ];
    }
}
