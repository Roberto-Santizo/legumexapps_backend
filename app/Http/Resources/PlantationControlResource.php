<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantationControlResource extends JsonResource
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
            'name' => $this->name,
            'start_date' => $this->start_date->format('d-m-Y'),
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y') : 'SIN FECHA FINAL',
            'lote' => $this->lote->name,
            'total_plants' => $this->total_plants
        ];
    }
}
