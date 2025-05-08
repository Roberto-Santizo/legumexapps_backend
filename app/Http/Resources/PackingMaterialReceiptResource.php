<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialReceiptResource extends JsonResource
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
            'receipt_date' => $this->receipt_date->format('d-m-Y'),
            'invoice_date' => $this->invoice_date->format('d-m-Y'),
            'observations' => $this->observations ?? '',
            'received_by' => $this->user->name,
            'received_by_signature' => $this->user_signature,
            'supervisor_name' => $this->supervisor_name,
            'supervisor_signature' => $this->supervisor_signature,
            'items' => PackingMaterialReceiptDetailsResource::collection($this->items)
        ];
    }
}
