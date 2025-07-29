<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftProductionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tasks = $this->tasks;
        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'confirmation_date' => $this->confirmation_date,
            'production_confirmation' => $this->production_confirmation ? true : false,
            'bodega_confirmation' => $this->bodega_confirmation  ? true : false,
            'logistics_confirmation' => $this->logistics_confirmatio  ? true : false,
            'tasks' => TaskProductionDraftResource::collection($this->tasks)
        ];
    }
}
