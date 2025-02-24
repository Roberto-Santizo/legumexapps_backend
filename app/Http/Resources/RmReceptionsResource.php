<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionsResource extends JsonResource
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
            'finca' => $this->finca->name,
            'plate' => $this->field_data->transport_plate,
            'product' => $this->field_data->product->name,
            'variety' => $this->field_data->product->variety->name,
            'coordinator' => $this->field_data->producer->name,
            'cdp' => $this->field_data->cdp,
            'transport' => $this->field_data->transport,
            'status' => $this->status
            
        ];
    }
}
