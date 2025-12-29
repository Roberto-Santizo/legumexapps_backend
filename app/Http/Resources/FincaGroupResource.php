<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FincaGroupResource extends JsonResource
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
            'code' => $this->code,
            'lote' => $this->lote->name,
            'finca' => $this->finca->name,
            'total_employees' => $this->employees->count(),
            'total_tasks' => $this->tasks->count(),
            'total_hours' => round($this->tasks->sum('hours'), 2),
        ];
    }
}
