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
            'budget' => $this->budget,
            'hours' => $this->hours,
            'week' => $this->week
        ];
    }
}
