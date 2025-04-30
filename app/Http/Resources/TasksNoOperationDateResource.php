<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksNoOperationDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $colors = [
            1 => 'bg-green-100',
            2 => 'bg-blue-100',
            4 => 'bg-red-100',
            5 => 'bg-orange-100',
            6 => 'bg-gray-100',
        ];

        return [
            'id' => strval($this->id),
            'task' => $this->task->name,
            'finca' => $this->plan->finca->name,
            'lote' => $this->lotePlantationControl->lote->name,
            'bg_color' => $colors[$this->lotePlantationControl->lote->finca_id],	
        ];
    }
}
