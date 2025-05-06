<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionOperationDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $working = ($this->start_date && !$this->end_date) ? true : false;
        return [
            'id' => strval($this->id),
            'sku' => $this->line_sku->sku->code,  
            'line' => $this->line->name,
            'total_lbs' => $this->total_lbs,
            'finished' => $this->end_date ? true : false,
            'working' => $working,
            'destination' => $this->destination 
        ];
    }
}
