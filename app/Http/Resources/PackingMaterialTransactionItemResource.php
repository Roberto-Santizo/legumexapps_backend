<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialTransactionItemResource extends JsonResource
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
            'description' => $this->item->name,
            'quantity' => $this->quantity,
            'destination' => $this->destination ?? $this->transaction->task->line_sku->line->name,
            'lote' => $this->lote,
        ];
    }
}
