<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionDraftResource extends JsonResource
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
            'total_boxes' => $this->total_boxes,
            'line' => $this->line->name,
            'sku' => $this->sku->code,
            'destination' => $this->destination
        ];
    }
}
