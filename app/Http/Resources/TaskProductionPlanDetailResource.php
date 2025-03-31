<?php

namespace App\Http\Resources;

use App\Models\TaskProductionTimeout;
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
        $total_boxes = $this->line_sku->sku->boxes_pallet ? ($this->performances->sum('tarimas_produced') * $this->line_sku->sku->boxes_pallet) : 0;
        $lbs_teoricas = $this->line_sku->sku->presentation ? ($total_boxes*$this->line_sku->sku->presentation) : 0;
        $performance_hours = $this->line_sku->lbs_performance ?( $lbs_teoricas/$this->line_sku->lbs_performance) : 0;
        foreach ($this->timeouts as $timeout) {
            $hours = 0;
            if($timeout->end_date){
                $hours = $timeout->start_date->diffInHours($timeout->end_date);
            }
            $line_hours -= $hours;
        }

        $this->performances->map(function($performance){
            $total_boxes = $performance->tarimas_produced * $this->line_sku->sku->boxes_pallet;
            $lbs_teoricas = $total_boxes*$this->line_sku->sku->presentation;
            $performance->lbs_teoricas = $lbs_teoricas;

            return $performance;
        });

        return [
            'line' => $this->line_sku->line->name,
            'sku' => $this->line_sku->sku->code,
            'start_date' => $this->start_date,
            'biometric_hours' => 8,
            'total_hours' => $this->total_hours ?? 0,
            'performance_hours' => round($performance_hours, 3),
            'line_hours' => round($line_hours, 3),
            'timeouts' => TaskProductionTimeoutResource::collection($this->timeouts),
            'performances' => TaskProductionPerformaceResource::collection($this->performances),
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees)
        ];
    }
}
