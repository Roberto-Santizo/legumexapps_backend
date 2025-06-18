<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $line_hours = $this->start_date->diffInHours(Carbon::now());
        $hours_timeouts = 0;

        //CALCULO DE HORAS RENDIMIENTO
        if (!$this->line_sku->payment_method) {
            $performance_hours = $this->performances->sum('lbs_bascula') / $this->line_sku->lbs_performance;
        } else {
            $performance_hours = $line_hours;
        }

        foreach ($this->timeouts as $timeout) {
            $hours = 0;
            if ($timeout->end_date) {
                $hours = $timeout->start_date->diffInHours($timeout->end_date);
            }
            $hours_timeouts += $hours;
        }

        $this->performances->map(function ($performance) {
            $total_boxes = $performance->tarimas_produced * $this->line_sku->sku->boxes_pallet;
            $lbs_teoricas = $total_boxes * $this->line_sku->sku->presentation;
            $performance->lbs_teoricas = $lbs_teoricas;

            return $performance;
        });

        return [
            'line' => $this->line_sku->line->name,
            'sku' => $this->line_sku->sku->code,
            'start_date' => $this->start_date,
            'HPlan' => $this->total_hours ? round($this->total_hours, 2) : 0,
            'HRendimiento' => round($performance_hours, 3),
            'HLinea' => round($line_hours, 3),
            'HTiemposMuertos' => round($hours_timeouts, 3),
            'timeouts' => TaskProductionTimeoutResource::collection($this->timeouts),
            'performances' => TaskProductionPerformaceResource::collection($this->performances),
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees)
        ];
    }
}
