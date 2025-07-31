<?php

namespace App\Http\Resources;

use App\Models\WeeklyProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftProductionPlanResourceDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plan_exists = WeeklyProductionPlan::where('year', $this->year)->where('week', $this->week)->first();

        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'plan_created' => $plan_exists ? true : false,
            'confirmation_date' => $this->confirmation_date ? $this->confirmation_date->format('d-m-Y h:i:s A') : '',
            'production_confirmation' => $this->production_confirmation ? true : false,
            'bodega_confirmation' => $this->bodega_confirmation  ? true : false,
            'logistics_confirmation' => $this->logistics_confirmation  ? true : false,
            'tasks' => TaskProductionDraftResource::collection($this->tasks)
        ];
    }
}
