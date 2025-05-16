<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipePackingMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $boxes = round($this->total_lbs / $this->line_sku->sku->presentation, 2);
        $bags = $boxes * $this->line_sku->sku->config_bag;
        return [
            'config_boxes' => $boxes,
            'config_bag' => $bags,
            'config_inner_bag' => $bags * $this->line_sku->sku->config_inner_bag
        ];
    }
}
