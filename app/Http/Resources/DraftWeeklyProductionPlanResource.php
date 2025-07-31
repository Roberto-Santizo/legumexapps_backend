<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftWeeklyProductionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $confirmation_date = $this->confirmation_date ? $this->confirmation_date->format('d-m-Y h:i:s A') : 'SIN FECHA DE CONFIRMACIÓN';
        $flag = $this->production_confirmation && $this->bodega_confirmation && $this->logistics_confirmation;
        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'confirmation_date' => $confirmation_date,
            'confirmed' => $flag
        ];
    }
}
