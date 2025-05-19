<?php

namespace App\Http\Resources;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionOperationDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = [
            0 => ['Pendiente entrega de material de empaque', 'bg-orange-500'],
            1 => ['Lista para ejecución', 'bg-blue-500'],
            2 => ['En progreso', 'bg-yellow-500'],
            3 => ['Finalizada', 'bg-green-500']
        ];

        $working = ($this->start_date && !$this->end_date) ? true : false;
        $boxes = round($this->total_lbs / $this->line_sku->sku->presentation, 2);
        $bags = $boxes * $this->line_sku->sku->config_bag;
        $inner_bags = $bags * $this->line_sku->sku->config_inner_bag;

        return [
            'id' => strval($this->id),
            'sku' => $this->line_sku->sku->code,
            'line' => $this->line->name,
            'total_lbs' => $this->total_lbs,
            'finished' => $this->end_date ? true : false,
            'working' => $working,
            'destination' => $this->destination,
            'status' => $status[$this->status][0],
            'status_id' => strval($this->status),
            'color' => $status[$this->status][1],
            'box' => $this->line_sku->sku->box->name,
            'bag' => $this->line_sku->sku->bag->name,
            'bag_inner'  => $this->line_sku->sku->bag_inner->name,
            'recipe' => [
                [
                    "packing_material_id" => strval($this->line_sku->sku->box_id),
                    "quantity" => $boxes,
                    "lote" => ""
                ],
                [
                    "packing_material_id" => strval($this->line_sku->sku->bag_id),
                    "quantity" => $bags,
                    "lote" => ""
                ],
                [
                    "packing_material_id" => strval($this->line_sku->sku->bag_inner_id),
                    "quantity" => $inner_bags,
                    "lote" => ""
                ]
            ]
        ];
    }
}
