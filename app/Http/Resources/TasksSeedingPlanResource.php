<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksSeedingPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "task" => $this->taskGuide->task->name,
            "cdp" => $this->cdp->name,
            "recipe" => $this->taskGuide->recipe->name,
            "budget" => $this->taskGuide->budget,
            "hours" => $this->taskGuide->hours
        ];
    }
}
