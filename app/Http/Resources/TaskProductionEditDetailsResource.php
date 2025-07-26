<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionEditDetailsResource extends JsonResource
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
            'sku_id' => strval($this->line_sku->sku->id),
            'line_id' => strval($this->line_id),
            'total_lbs' => $this->total_lbs,
            'destination' => $this->destination,
            'operation_date' => $this->operation_date ? $this->operation_date->format('Y-m-d') : ''
        ];
    }
}
