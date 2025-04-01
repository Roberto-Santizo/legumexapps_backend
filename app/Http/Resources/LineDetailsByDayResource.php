<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LineDetailsByDayResource extends JsonResource
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
            'sku_description' => $this->line_sku->sku->product_name,
            'lbs_produced' => $this->total_lbs_produced,
            'start_date' => $this->start_date->format('d-m-Y h:i:s A'),
            'end_date' => $this->end_date->format('d-m-Y h:i:s A')
        ];
    }
}
