<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskWeeklyPlanDraftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "task_guideline_id" => $this->task_guideline_id,
            "draft_weekly_plan_id" => $this->draft_weekly_plan_id,
            "hours" => $this->hours,
            "budget" => $this->budget,
            "slots" => $this->slots,
            "tags" => $this->tags,
            "insumos" => TaskInsumoRecipeResource::collection($this->taskGuide->insumos)
        ];
    }
}
