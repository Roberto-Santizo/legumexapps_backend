<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lbs_planta' => $this->lbs_planta,
            'lbs_finca' => $this->lbs_finca,
            'start_hour' => $this->start_date->format('h:i:s A'),
            'end_hour' => $this->end_date->format('h:i:s A'),
            'date' => $this->end_date,
            'plants' => $this->plants
        ];
    }
}
