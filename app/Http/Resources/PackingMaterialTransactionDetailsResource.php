<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialTransactionDetailsResource extends JsonResource
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
            'type' => $this->type,
            'transaction_date' => $this->created_at->format('d-m-Y h:i:s A'),
            'transaction' => $this->reference,
            'items' => PackingMaterialTransactionItemResource::collection($this->items),
            'observations' => $this->observations ?? '',
            'delivered_by' => $this->user->name,
            'delivered_by_signature' => $this->user_signature,
            'responsable' => $this->responsable,
            'responsable_signature' => $this->responsable_signature,
        ];
    }
}
