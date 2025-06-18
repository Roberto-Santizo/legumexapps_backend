<?php

namespace App\Http\Resources;

use App\Models\BiometricTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $positions = $this->line_sku->line->positions->filter(function ($position) {
            $assignee = $this->employees()->where('position', $position->name)->first();
            if (!$assignee) {
                return $position;
            }
        });

        $employees = $this->employees->filter(function ($employee) {
            $today = Carbon::today();
            return !(BiometricTransaction::where('last_name', $employee->position)->whereDate('event_time', $today)->exists());
        });

        return [
            'id' => strval($this->id),
            'line' => $this->line_sku->line->code,
            'operation_date' => $this->operation_date,
            'start_date' => $this->start_date,
            'assigned_employees' => $this->employees->count(),
            'flag' => $this->employees->count() < $this->line_sku->line->positions->count(),
            'total_lbs' => $this->total_lbs,
            'sku' => new SKUResource($this->line_sku->sku),
            'filtered_employees' => TaskProductionEmployeeResource::collection($employees),
            'positions' => PositionResource::collection($positions)
        ];
    }
}
