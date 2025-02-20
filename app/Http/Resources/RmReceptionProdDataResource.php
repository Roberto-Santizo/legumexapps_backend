<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionProdDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->prod_data->id,
            'total_baskets' => $this->prod_data->total_baskets,
            'weight_baskets' => $this->field_data->basket->weight,
            'gross_weight' => $this->prod_data->gross_weight,
            'tara' => $this->prod_data->total_baskets * $this->field_data->basket->weight,
            'net_weight' => $this->prod_data->net_weight,
            'receptor_signature' => $this->prod_data->receptor_signature
        ];
    }
}