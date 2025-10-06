<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RmReceptionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $net_weight = $this->field_data->weight -  $this->field_data->weight_baskets;
        // $driver_signature = base64_encode(file_get_contents(storage_path("app/public/{$this->field_data->driver_signature}")));
        // $inspector_signature = base64_encode(file_get_contents(storage_path("app/public/{$this->field_data->inspector_signature}")));
        // $producer_signature = base64_encode(file_get_contents(storage_path("app/public/{$this->field_data->prod_signature}")));

        return [
            'id' => strval($this->id),
            'ref_doc' => $this->id,
            'producer_id' => strval($this->field_data->producer->id),
            'producer_code' => $this->field_data->producer->code,
            'product_id' => strval($this->field_data->product->id),
            'plate' => $this->field_data->plate->name,
            'product' => $this->field_data->product->name,
            'variety' => $this->field_data->product->variety->name,
            'coordinator' => $this->field_data->producer->name,
            'inspector' => $this->field_data->inspector_name,
            'pilot_name' => $this->user->name,
            'doc_date' => $this->doc_date->format('d-m-Y'),
            'cdp' => $this->field_data->cdp->name,
            'transport' => $this->field_data->carrier->code,
            'baskets' => $this->field_data->total_baskets,
            'weight_basket' => $this->field_data->basket->weight,
            'gross_weight' => $this->field_data->weight,
            'weight_baskets' => $this->field_data->weight_baskets,
            'net_weight' =>  $net_weight,
            'prod_net_weight' => $this->prod_data->net_weight ?? null,
            'percentage_field' => $this->field_data->quality_percentage,
            'valid_pounds' => (($this->field_data->quality_percentage / 100) * $net_weight),
            'status' => $this->status->name,
            'quality_status_id' => $this->quality_status_id,
            'minimun_percentage' => $this->field_data->product->accepted_percentage,
            'total_baskets' => $this->field_data->total_baskets,
            'driver_signature' => $this->field_data->driver_signature,
            'inspector_signature' => $this->field_data->inspector_signature,
            'producer_signature' => $this->field_data->prod_signature,
            'driver_pdf_signature' => "",
            'inspector_pdf_signature' => "",
            'producer_pdf_signature' => "",

        ];
    }
}
