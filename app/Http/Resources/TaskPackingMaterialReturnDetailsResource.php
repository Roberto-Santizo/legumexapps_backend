<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskPackingMaterialReturnDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lbs = $this->total_lbs - $this->total_lbs_bascula;
        $boxes = round($lbs / $this->line_sku->sku->presentation, 2);
        $bags = $boxes * $this->line_sku->sku->config_bag;
        $inner_bags = $bags * $this->line_sku->sku->config_inner_bag;
        $first_transaction = $this->transactions->first();


        $new_quantity = [
            $this->line_sku->sku->box_id => $boxes,
            $this->line_sku->sku->bag_id => $bags,
            $this->line_sku->sku->bag_inner_id => $inner_bags,
        ];

        $flag = $this->transactions()->where('type',2)->exists() ? true : false;
        return [
            'available' => !$flag,
            'items' => $first_transaction->items->map(function ($item) use ($new_quantity) {
                return [
                    "name" => $item->item->name,
                    'packing_material_id' => strval($item->packing_material_id),
                    'quantity' => $new_quantity[$item->packing_material_id],
                    'lote' => $item->lote,
                    'destination' => 'BODEGA'
                ];
            })
        ];
    }
}
