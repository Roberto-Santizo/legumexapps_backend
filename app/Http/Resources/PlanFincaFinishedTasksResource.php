<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanFincaFinishedTasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $percentage = ($this->tasks()->whereNot('end_date',null)->count()/$this->tasks()->count())*100;
        return [
            'id' => strval($this->id),
            'finca' => $this->finca->name,
            'finished_tasks' => $this->tasks()->whereNot('end_date',null)->count(),
            'total_tasks' => $this->tasks()->count(),
            'percentage' => $percentage
        ];
    }
}
