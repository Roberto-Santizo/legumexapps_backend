<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCropResource extends JsonResource
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
            'task' => $this->task->name,
            'cultivo' => $this->task->crop->name,
            'assigment_today' => ($this->assignment_today) ? true : false,
            'finished_assigment_today' => ($this->assignment_today && $this->assignment_today->end_date) ? true : false
        ];
    }
}
