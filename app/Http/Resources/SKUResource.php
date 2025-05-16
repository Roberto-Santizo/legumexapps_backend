<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SKUResource extends JsonResource
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
            'code' => $this->code,
            'product_name' => $this->product_name,
            'presentation' => $this->presentation ? strval($this->presentation) : 'SIN PRESENTACIÓN',
            'client_name' => $this->client_name,
            'box' => $this->box->code,
            'bag' => $this->bag->code,
            'bag_inner' => $this->bag_inner->code
        ];
    }
}
