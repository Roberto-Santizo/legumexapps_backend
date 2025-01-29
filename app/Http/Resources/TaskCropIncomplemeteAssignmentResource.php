<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCropIncomplemeteAssignmentResource extends JsonResource
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
            'lbs_finca' => $this->lbs_finca,
            'lbs_planta' => $this->lbs_planta,
            'date' =>  $this->start_date
        ];
    }
}
