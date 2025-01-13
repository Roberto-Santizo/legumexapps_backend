<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantationControlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $aplication_week = 0;
        if($this->end_date)
        {
            $aplication_week = ($this->end_date->year - $this->start_date->year) * 52 + ($this->end_date->weekOfYear - $this->start_date->weekOfYear );
        }else{
            $aplication_week = (Carbon::now()->year - $this->start_date->year) * 52 + (Carbon::now()->weekOfYear - $this->start_date->weekOfYear);
        }
        return [
            'id' => strval($this->id),
            'name' => $this->name,
            'crop' => $this->crop->name,
            'recipe' => $this->recipe->name,
            'density' => $this->density,
            'start_date' => $this->start_date->format('d-m-Y'),
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y') : 'SIN FECHA FINAL',
            'size' => $this->size,
            'aplication_week' => $aplication_week,
            'status' => $this->end_date ? true : false
        ];
    }
}
