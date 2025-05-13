<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsumosReceiptItemsResource extends JsonResource
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
            'code' => $this->insumo->code,
            'measure' => $this->insumo->measure,
            'name' => $this->insumo->name,
            'units' => $this->units,
        ];
    }
}
