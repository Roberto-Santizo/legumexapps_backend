<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPerformaceResource extends JsonResource
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
            'take_date' => $this->created_at->format('d-m-Y h:i:s A'),
            'tarimas_produced' => $this->tarimas_produced,
            'lbs_bascula' => $this->lbs_bascula,
        ];
    }
}
