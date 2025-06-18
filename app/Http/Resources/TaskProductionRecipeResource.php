<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionRecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public $total_lbs;

    public function __construct($resource, $total_lbs = null)
    {
        parent::__construct($resource);
        $this->total_lbs = $total_lbs;
    }

    public function toArray(Request $request): array
    {
        $quantity = round($this->total_lbs/$this->lbs_per_item,2);

        return    [
            "packing_material_id" => strval($this->item_id),
            "name" => $this->item->name,
            "code" => $this->item->code,
            "quantity" => $quantity,
            "lote" => "",
            "destination" => ""
        ];
    }
}
