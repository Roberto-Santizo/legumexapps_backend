<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class SeedingPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cdp = $this->additional['filter'];
        $tasks = [];
        if ($cdp) {
            $tasks = $this->tasks()->whereHas('cdp', function ($q) use ($cdp) {
                $q->where('name', 'LIKE', '%' . $cdp . '%');
            })->get();

        } else {
            $tasks = $this->tasks;
        }

        return [
            "id" => $this->id,
            "finca" => $this->finca->name,
            "week" => $this->week,
            "status" => $this->status,
            "tasks" =>  ($tasks->count() > 0) ? TasksSeedingPlanResource::collection($tasks)->groupBy('cdp.name') : new stdClass()
        ];
    }
}
