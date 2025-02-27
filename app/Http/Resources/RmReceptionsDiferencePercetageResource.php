<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionsDiferencePercetageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'finca' => $this->finca->name,
            'field_percentage' => $this->field_data->quality_percentage,
            'quality_percentage' => $this->quality_control_doc_data->percentage
        ];
    }
}
