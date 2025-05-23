<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialDispatchResource extends JsonResource
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
            'reference' => $this->reference,
            'responsable' => $this->responsable,
            'user' => $this->user->name,
            'dispatch_date' => $this->created_at->format('d-m-Y h:i:s A')
        ];
    }
}
