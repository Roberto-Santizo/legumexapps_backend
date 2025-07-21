<?php

namespace App\Http\Resources;

use App\Models\BiometricTransaction;
use App\Models\TaskProductionPlan;
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
        $today = Carbon::today();

        $positions = $this->line_sku->line->positions->filter(function ($position) {
            $assignee = $this->employees()->where('position', $position->name)->first();
            if (!$assignee) {
                return $position;
            }
        });

        $employees = $this->employees->filter(function ($employee) use ($today) {
            return !(BiometricTransaction::where('last_name', $employee->position)->whereDate('event_time', $today)->exists());
        });

        $last_task = TaskProductionPlan::where('line_id', $this->line_id)
            ->where('operation_date', $today)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get()
            ->last();

        $exists_previuos_config = $last_task ? true : false; 

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
            'all_employees' => TaskProductionEmployeeResource::collection($this->employees),
            'exists_previuos_config' => $exists_previuos_config,
            'positions' => PositionResource::collection($positions)
        ];
    }
}
