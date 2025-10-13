<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeedingPlanResource extends JsonResource
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
            "finca" => $this->finca->name,
            "week" => $this->week,
            "tasks" => TasksSeedingPlanResource::collection($this->tasks)->groupBy('cdp.name')
        ];
    }
}
