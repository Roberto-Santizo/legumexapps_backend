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
            "lote" => $this->cdp->lote->name,
            "recipe" => $this->taskGuide->recipe->name,
            "budget" => $this->budget,
            "hours" => $this->hours,
            "tags" => $this->tags,
            "slots" => $this->slots,
            "draft_weekly_plan_id" => $this->draft_weekly_plan_id

        ];
    }
}
