<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionForCalendarResource extends JsonResource
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
            'title' => $this->line->code . ' - ' . $this->sku->code,
            'start' => $this->operation_date->format('Y-m-d'),
            'priority' => strval($this->priority)
        ];
    }
}
