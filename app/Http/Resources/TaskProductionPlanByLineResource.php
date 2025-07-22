<?php

namespace App\Http\Resources;

use App\Models\BiometricTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanByLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray(Request $request): array
    {
        static $presentPositions = null;

        if (is_null($presentPositions)) {
            $presentPositions = BiometricTransaction::whereDate('event_time', Carbon::today())
                ->pluck('last_name')
                ->toArray();
        }

        $total_hours = 0;
        $paused = false;

        if ($this->timeouts->isNotEmpty()) {
            $paused = is_null($this->timeouts->last()->end_date);
        }

        if ($this->start_date && $this->end_date) {
            $total_hours = $this->start_date->diffInHours($this->end_date);
        }

        $total_in_employees = $this->employees->filter(function ($employee) use ($presentPositions) {
            return in_array($employee->position, $presentPositions);
        });

        return [
            'id' => strval($this->id),
            'line' => $this->line_sku->line->code,
            'sku' => $this->line_sku->sku->code,
            'product' => $this->line_sku->sku->product_name,
            'total_lbs' => $this->total_lbs,
            'operation_date' => $this->operation_date->format('d-m-Y'),
            'start_date' => optional($this->start_date)->format('d-m-Y h:i:s A'),
            'end_date' => optional($this->end_date)->format('d-m-Y h:i:s A'),
            'hours' => $this->total_hours,
            'total_hours' => $total_hours,
            'total_in_employees' => $total_in_employees->count(),
            'total_employees' => $this->employees->count(),
            'priority' => $this->priority,
            'paused' => $paused,
            'is_minimum_requrire' => (bool) $this->is_minimum_require,
            'is_justified' => (bool) $this->is_justified,
            'status' => $this->status
        ];
    }
}
