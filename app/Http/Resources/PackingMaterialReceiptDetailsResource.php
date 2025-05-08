<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialReceiptDetailsResource extends JsonResource
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
            'code' => $this->item->code,
            'description' => $this->item->description,
            'supplier' => $this->item->supplier->name,
            'lote' => $this->lote,
            'quantity' => $this->quantity
        ];
    }
}
