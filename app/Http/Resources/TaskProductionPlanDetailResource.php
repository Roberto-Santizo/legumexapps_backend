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
        $performance_hours = 0;
        $line_hours = $this->start_date->diffInHours(Carbon::now());

        foreach ($this->timeouts as $timeout) {
            $hours = 0;
            if($timeout->end_date){
                $hours = $timeout->start_date->diffInHours($timeout->end_date);
            }
            $line_hours -= $hours;
        }
        foreach ($this->performances as $performance) {
            $performance_hours += $performance->tarimas_produced / 2;
        }

        return [
            'line' => $this->line_sku->line->name,
            'sku' => $this->line_sku->sku->code,
            'start_date' => $this->start_date,
            'biometric_hours' => 8,
            'total_hours' => $this->total_hours,
            'performance_hours' => round($performance_hours, 3),
            'line_hours' => round($line_hours, 3),
            'timeouts' => TaskProductionTimeoutResource::collection($this->timeouts),
            'performances' => TaskProductionPerformaceResource::collection($this->performances),
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees)
        ];
    }
}
