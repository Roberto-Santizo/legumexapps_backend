<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total_employees = $this->employees->count();
        return [
            'id' => strval($this->id),
            'line' => $this->line_sku->line->code,
            'operation_date' => $this->operation_date,
            'total_lbs' => $this->total_lbs,
            'sku' => new SKUResource($this->line_sku->sku),
            'employees' => TaskProductionEmployeeResource::collection($this->employees)
        ];
    }
}
