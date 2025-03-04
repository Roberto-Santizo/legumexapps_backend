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
            'id' => $this->transport_doc_data->transport_data->id,
            'verify_by' => $this->transport_doc_data->transport_data->user->name,
            'planta' => $this->transport_doc_data->transport_data->planta->name,
            'pilot_name' => $this->transport_doc_data->transport_data->pilot_name,
            'product' => $this->transport_doc_data->transport_data->product->name . ' ' . $this->transport_doc_data->transport_data->product->variety->name,
            'truck_type' => $this->transport_doc_data->transport_data->truck_type,
            'date' => $this->transport_doc_data->transport_data->date->format('d-m-Y'),
            'plate' => $this->transport_doc_data->transport_data->plate,
            'observations' => $this->transport_doc_data->transport_data->observations,
            'verify_by_signature' => $this->transport_doc_data->transport_data->verify_by_signature,
            'quality_manager_signature' => $this->transport_doc_data->transport_data->quality_manager_signature,
            'conditions' =>  TransportInspectionConditionResource::collection($this->transport_doc_data->transport_data->conditions)
        ];
    }
}
