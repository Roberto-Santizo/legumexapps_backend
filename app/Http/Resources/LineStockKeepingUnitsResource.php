<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LineStockKeepingUnitsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shift = $this->line->shift ? 'AM' : 'PM';
        return [
            'id' => strval($this->id),
            'line' => $this->line->name,
            'sku' => $this->sku->code,
            'client' => $this->client->name,
            'product' => $this->sku->product->name,
            'shift' => $shift,
            'performance' => $this->lbs_performance
        ];
    }
}
