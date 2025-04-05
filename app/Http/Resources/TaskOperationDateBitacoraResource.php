<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskOperationDateBitacoraResource extends JsonResource
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
            'user' => $this->user->name,
            'reason' => $this->reason,
            'original_date' => $this->original_date->format('d-m-Y h:i:s A'),
            'new_date' => $this->new_date->format('d-m-Y h:i:s A'),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
