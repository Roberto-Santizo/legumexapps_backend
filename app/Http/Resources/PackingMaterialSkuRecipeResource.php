<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialSkuRecipeResource extends JsonResource
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
            'product' => $this->item->name,
            'code' => $this->item->code,
            'lbs_per_item' => $this->lbs_per_item
        ];
    }
}
