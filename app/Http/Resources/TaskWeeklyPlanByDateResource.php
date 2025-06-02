<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskWeeklyPlanByDateResource extends JsonResource
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
            'lote' => $this->lotePlantationControl->lote->name,
            'operation_date' => $this->operation_date,
            'status' => $this->prepared_insumos ? true : false,
            'insumos' => TaskInsumosResource::collection($this->insumos)
        ];
    }
}
