<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanNoOperationDateResource extends JsonResource
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
            'sku' => $this->line_sku->sku->code,  
            'line' => $this->line->name,
            'total_lbs' => $this->total_lbs,
            'product_name'=> $this->line_sku->sku->product_name,
            'destination' => $this->destination
        ];
    }
}
