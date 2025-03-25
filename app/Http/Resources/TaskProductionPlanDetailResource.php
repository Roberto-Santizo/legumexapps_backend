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
        $performance_hours = 0;
        $line_hours = $this->start_date->diffInHours(Carbon::now());

        foreach ($this->performances as $performance) {
            $performance_hours += $performance->tarimas_produced/2;
        }
        return [
            'line' => $this->line->code,
            'sku' => $this->sku->name,
            'start_date' => $this->start_date,
            'biometric_hours' => 8,
            'last_take' => $this->performances->last()->created_at->format('d-m-Y H:m:i A'),
            'last_finished_tarimas' => $this->performances->last()->tarimas_produced,
            'total_hours' => $this->total_hours,
            'performance_hours' => round($performance_hours,3),
            'line_hours' => round($line_hours,3),
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees)
        ];
    }
}
