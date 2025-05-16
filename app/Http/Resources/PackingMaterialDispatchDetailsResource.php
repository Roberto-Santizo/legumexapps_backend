<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingMaterialDispatchDetailsResource extends JsonResource
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
            'dispatch_date' => $this->created_at->format('d-m-Y h:i:s A'),
            'items' => [
                [
                    'code' => $this->task->line_sku->sku->box->code,
                    'description' => $this->task->line_sku->sku->box->name,
                    'destination' => 'DESTINATION',
                    'lote' => 'LOTE',
                    'quantity' => $this->quantity_boxes,
                ],
                [
                    'code' => $this->task->line_sku->sku->bag->code,
                    'description' => $this->task->line_sku->sku->bag->name,
                    'destination' => 'DESTINATION',
                    'lote' => 'LOTE',
                    'quantity' => $this->quantity_bags,
                ],
                [
                    'code' => $this->task->line_sku->sku->bag_inner->code,
                    'description' => $this->task->line_sku->sku->bag_inner->name,
                    'destination' => 'DESTINATION',
                    'lote' => 'LOTE',
                    'quantity' => $this->quantity_inner_bags,
                ]
            ],
            'observations' => $this->observations ?? '',
            'delivered_by' => $this->user->name,
            'delivered_by_signature' => $this->user_signature,
            'received_boxes_by' => $this->received_by_boxes,
            'received_boxes_by_signature' => $this->received_by_signature_boxes,
            'received_bags_by' => $this->received_by_bags,
            'received_bags_by_signature' => $this->received_by_signature_bags,
        ];
    }
}
