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
        $first_transaction = $this->transactions->first();
        $rows = [];

        foreach ($first_transaction->items as $item) {
            $difference = $this->total_lbs - $this->total_lbs_bascula;
            $item_recipe = $this->line_sku->sku->items()->where('item_id',$item->packing_material_id)->first();
            $quantity = $difference/$item_recipe->lbs_per_item;
            
            $rows[] = [
                'name' => $item->item->name,
                'packing_material_id' => strval($item->item->id),
                'quantity' => $quantity,
                'lote' => $item->lote,
                'destination' => $item->destination ?? $this->line_sku->line->name,
                'code' => $item->item->code
            ];
        }
        
        $flag = $this->transactions()->where('type', 2)->exists() ? true : false;
        return [
            'available' => !$flag,
            'items' => $rows
        ];
    }
}
