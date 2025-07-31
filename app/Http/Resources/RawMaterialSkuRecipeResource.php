<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialSkuRecipeResource extends JsonResource
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
            'product' => $this->item->product_name,
            'code' => $this->item->code,
            'percentage' => $this->percentage
        ];
    }
}
