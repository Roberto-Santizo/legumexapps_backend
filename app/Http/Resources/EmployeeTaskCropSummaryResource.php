<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTaskCropSummaryResource extends JsonResource
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
            'daily_assignment_id' => $this->daily_assignment_id,
            'name' => $this->name,
            'code' => $this->code,
            'lbs' => $this->lbs ?? 0,
            'date' => $this->assignment->start_date
        ];
    }
}
