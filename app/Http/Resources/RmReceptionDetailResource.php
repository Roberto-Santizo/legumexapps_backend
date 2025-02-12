<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $net_weight = $this->field_data->weight -  $this->field_data->weight_baskets;
        return [
            'id' => strval($this->id),
            'plate' => $this->field_data->transport_plate,
            'product' => $this->field_data->product->name,
            'variety' => $this->field_data->product->variety->name,
            'coordinator' => $this->field_data->coordinator_name,
            'cdp' => $this->field_data->cdp,
            'transport' => $this->field_data->transport,
            'baskets' => $this->field_data->total_baskets,
            'gross_weight' => $this->field_data->weight,
            'weight_baskets' => $this->field_data->weight_baskets,
            'net_weight' =>  $net_weight,
            'percentage_field' => $this->field_data->quality_percentage,
            'valid_pounds' => (($this->field_data->quality_percentage/100)*$net_weight),
            'status' => $this->status   

        ];
    }
}
