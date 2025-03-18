<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiometricEmployeeResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->last_name,
            'position' => $this->pin,
            'column_id' => strval(2)
        ];
    }
}
