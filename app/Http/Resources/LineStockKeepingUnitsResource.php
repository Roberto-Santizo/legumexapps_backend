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
            'client' => $this->sku->client_name,
            'product' => $this->sku->product_name,
            'payment_method' => $this->payment_method,
            'shift' => $shift,
            'performance' => $this->lbs_performance ? $this->lbs_performance : null,
            'accepted_percentage' => $this->accepted_percentage
        ];
    }
}
