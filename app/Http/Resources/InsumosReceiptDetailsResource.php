<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsumosReceiptDetailsResource extends JsonResource
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
            'received_date' => $this->received_date->format('d-m-Y h:i:s A'),
            'invoice' => $this->invoice,
            'supplier' => $this->supplier->name,
            'invoice_date' => $this->invoice_date->format('d-m-Y'),
            'supervisor_signature' => $this->supervisor_signature,
            'user_signature' => $this->user_signature,
            'items' => InsumosReceiptItemsResource::collection($this->items)
        ];
    }
}
