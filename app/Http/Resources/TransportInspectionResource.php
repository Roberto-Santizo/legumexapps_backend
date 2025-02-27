<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportInspectionResource extends JsonResource
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
            'pilot_name' => $this->pilot_name,
            'truck_type' => $this->truck_type,
            'plate' => $this->plate,
            'date' => $this->date->format('d-m-Y'),
            'product' => $this->rm_reception->field_data->product->name,
            'variety' => $this->rm_reception->field_data->product->variety->name,
            'finca' => $this->rm_reception->finca->name,
            'cdp' => $this->rm_reception->field_data->cdp,
            'planta' => $this->planta->name

        ];
    }
}
