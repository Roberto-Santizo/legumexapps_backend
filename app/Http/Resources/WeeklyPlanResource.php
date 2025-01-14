<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyPlanResource extends JsonResource
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
            'year' => $this->year,
            'week' => $this->week,
            'finca' => $this->finca->name,
            'created_at' => $this->created_at->format('d-m-Y'),
            'budget' => 'Q10/Q100',
            'budget_ext' => 'Q1/Q10',
            'tasks' => '1/1',
            'tasks_crop' => '0/0',
       ];
    }
}
