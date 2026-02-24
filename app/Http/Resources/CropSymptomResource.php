<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CropSymptomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->map(function ($symptom) {
            return [
                'id' => $symptom->id,
                'symptom' => $symptom->symptom,
                'part' => $symptom->cropPart->name,
                'disease' => $symptom->disease->name,
                'cropDiseaseId' => $symptom->crop_disease_id,
                'crop_disease_id' =>  $symptom->crop_disease_id,
                'crop_part_id' => $symptom->crop_part_id
            ];
        })->toArray();
    }
}
