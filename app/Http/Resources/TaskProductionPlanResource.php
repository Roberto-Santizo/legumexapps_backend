<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total_hours = 0;
        if($this->start_date && $this->end_date){
            $total_hours = $this->start_date->diffInHours($this->end_date);
        }
        return [
            'id' => strval($this->id),
            'line' => $this->line->code,
            'total_tarimas' => $this->skus->sum('tarimas'),
            'operation_date' => $this->operation_date,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'hours' => $this->total_hours,
            'total_hours' => $total_hours
        ];
    }
}
