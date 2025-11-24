<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskGuidelineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task' => $this->task->name,
            'recipe' => $this->recipe->name,
            'crop' => $this->crop->name,
            'finca' => $this->finca->name,
            'hours_per_size' => $this->hours_per_size,
            'week' => $this->week
        ];
    }
}
