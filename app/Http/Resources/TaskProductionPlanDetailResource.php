<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'line' => $this->line->code,
            'sku' => $this->sku->name,
            'start_date' => $this->start_date,
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees)
        ];
    }
}
