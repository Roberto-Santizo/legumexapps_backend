<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionTransportDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->transport_doc_data->id,
            'pilot_name' => $this->transport_doc_data->pilot_name,
            'product' => $this->transport_doc_data->product->name . ' ' . $this->transport_doc_data->product->variety->name,
            'truck_type' => $this->transport_doc_data->truck_type,
            'date' => $this->transport_doc_data->date->format('d-m-Y'),
            'plate' => $this->transport_doc_data->plate,
            'conditions' =>  TransportInspectionConditionResource::collection($this->transport_doc_data->conditions)
        ];
    }
}
