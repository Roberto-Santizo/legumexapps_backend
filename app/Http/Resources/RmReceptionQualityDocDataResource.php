<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionQualityDocDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->quality_control_doc_data->id,
            'percentage' => $this->quality_control_doc_data->percentage,
            'valid_pounds' => $this->quality_control_doc_data->valid_pounds,
            'inspector_planta_signaure' => $this->quality_control_doc_data->inspector_signature,
            'date' => $this->quality_control_doc_data->doc_date->format('d-m-Y'),
            'day' => $this->quality_control_doc_data->doc_date->format('d'),
            'month' =>$this->quality_control_doc_data->doc_date->format('m'),
            'year' => $this->quality_control_doc_data->doc_date->format('Y'),
            'time' => $this->quality_control_doc_data->doc_date->format('h:i:s A'),
            'producer_name' => $this->quality_control_doc_data->producer->name,
            'variety' => $this->field_data->product->variety->name,
            'grn' => $this->grn,
            'net_weight' => $this->quality_control_doc_data->net_weight,
            'no_hoja_cosechero' => $this->quality_control_doc_data->no_doc_cosechero,
            'sample_units' => $this->quality_control_doc_data->sample_units,
            'total_baskets' => $this->quality_control_doc_data->total_baskets,
            'producer_code' =>  $this->quality_control_doc_data->producer->code,
            'ph' =>  $this->quality_control_doc_data->ph,
            'brix' =>  $this->quality_control_doc_data->brix,
            'observations' =>  $this->quality_control_doc_data->observations,
            'inspector_planta_name' => $this->quality_control_doc_data->user->name,
            'defects' => $this->quality_control_doc_data->defects->map(function ($defect) {
                return [
                    'id' => $defect->id,
                    'name' => $defect->defect->name,
                    'input_percentage' => $defect->input,
                    'tolerace_percentage' => $defect->tolerance_percentage,
                    'result' => $defect->result,
                ];
            }),
            'total_defects_evaluation' => 100 - $this->quality_control_doc_data->percentage,
        ];
    }
}
