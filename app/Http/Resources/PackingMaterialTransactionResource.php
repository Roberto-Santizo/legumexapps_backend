<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $types = [
            0 => 'Entrada',
            1 => 'Salida',
            2 => 'Devoluición',
        ];

        return [
            'id' => strval($this->id),
            'reference' => $this->reference,
            'responsable' => $this->responsable,
            'user' => $this->user->name,
            'transaction_date' => $this->created_at->format('d-m-Y h:i:s A'),
            'type' => $types[$this->type],
        ];
    }
}   
