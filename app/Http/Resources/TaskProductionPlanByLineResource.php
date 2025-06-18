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
        $total_hours = 0;
        $paused = false;
        if($this->timeouts->count() > 0){
            $paused =( $this->timeouts->last()->end_date === null) ? true : false;
        }

        if ($this->start_date && $this->end_date) {
            $total_hours = $this->start_date->diffInHours($this->end_date);
        }

        $total_in_employees = $this->employees->filter(function ($employee) {
            $today = Carbon::today();
            return !(BiometricTransaction::where('last_name', $employee->position)->whereDate('event_time', $today)->exists());
        });

        return [
            'id' => strval($this->id),
            'line' => $this->line_sku->line->code,
            'sku' => $this->line_sku->sku->code,
            'product' => $this->line_sku->sku->product_name,
            'total_lbs' => $this->total_lbs,
            'operation_date' => $this->operation_date->format('d-m-Y'),
            'start_date' => $this->start_date ? $this->start_date->format('d-m-Y h:i:s A') : null,
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y h:i:s A') : null,
            'hours' => $this->total_hours,
            'total_hours' => $total_hours,
            'total_in_employees' => ($this->employees->count() - $total_in_employees->count()),
            'total_employees' => $this->employees->count(),
            'priority' => $this->priority,
            'paused' => $paused,
            'is_minimum_requrire' => $this->is_minimum_require ? true : false,
            'is_justified' => $this->is_justified ? true : false,
            'status' => $this->status
        ];
    }
}
