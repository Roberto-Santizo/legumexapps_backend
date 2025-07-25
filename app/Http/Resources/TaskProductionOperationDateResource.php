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
            1 => ['Pendiente entrega de material de empaque', 'bg-orange-500'],
            2 => ['Lista para confirmación de asignaciones', 'bg-blue-500'],
            3 => ['Lista para ejecución', 'bg-blue-500'],
            4 => ['En progreso', 'bg-yellow-500'],
            5 => ['Finalizada', 'bg-green-500']
        ];

        $working = ($this->start_date && !$this->end_date) ? true : false;
        $total_lbs =  $this->total_lbs;
        $has_employees = $this->employees->count() > 0 ? true : false;

        return [
            'id' => strval($this->id),
            'sku' => $this->line_sku->sku->code,
            'product' => $this->line_sku->sku->product_name,
            'line' => $this->line->name,
            'total_lbs' => $this->total_lbs,
            'finished' => $this->end_date ? true : false,
            'working' => $working,
            'destination' => $this->destination ?? 'SIN DESTINO ASOCIADO',
            'has_employees' => $has_employees,
            'status' => $status[$this->status][0],
            'status_id' => strval($this->status),
            'color' => $status[$this->status][1],
            'recipe' => $this->line_sku->sku->items->map(function ($item) use ($total_lbs) {
                return new TaskProductionRecipeResource($item, $total_lbs);
            })
        ];
    }
}
