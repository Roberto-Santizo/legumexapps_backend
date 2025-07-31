<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockKeepingUnitResource extends JsonResource
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
            'client_name' => $this->client_name,
            'packing_material_recipe' => PackingMaterialSkuRecipeResource::collection($this->items),
            'raw_material_recipe' => RawMaterialSkuRecipeResource::collection($this->products)
        ];
    }
}
