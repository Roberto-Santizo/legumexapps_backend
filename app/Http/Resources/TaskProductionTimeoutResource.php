<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionTimeoutResource extends JsonResource
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
            'name' => $this->timeout->name,
            'start_date' => $this->start_date->format('d-m-Y h:i:s A'),
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y h:i:s A') : null,
            'total_hours' => $this->end_date ? round($this->start_date->diffInHours($this->end_date),4) : null
        ];
    }
}
