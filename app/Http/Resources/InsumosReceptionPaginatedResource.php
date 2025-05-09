<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsumosReceptionPaginatedResource extends JsonResource
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
            'received_by' => $this->user->name,
            'invoice' => $this->invoice,
            'supplier' => $this->supplier->name,
            'received_date' => $this->received_date->format('d-m-Y h:i:s A'),
            'invoice_date' => $this->invoice_date->format('d-m-Y'),
            'supplier' => $this->supplier->name,
        ];
    }
}
