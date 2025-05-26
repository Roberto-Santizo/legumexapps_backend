<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanResource extends JsonResource
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
            'line' => $this->line_sku->line->name, 
            'product' => $this->line_sku->sku->product_name, 
            'code' => $this->line_sku->sku->code, 
            'operation_date' => $this->operation_date->format('d-m-Y'), 
        ];
    }
}
