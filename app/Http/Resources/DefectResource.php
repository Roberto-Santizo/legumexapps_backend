<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefectResource extends JsonResource
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
            'tolerance_percentage' => $this->tolerance_percentage,
            'status' => $this->status ? true : false,
            'quality_variety' => $this->quality_variety->name
        ];
    }
}
