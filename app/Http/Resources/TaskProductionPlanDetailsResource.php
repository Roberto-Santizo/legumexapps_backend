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

        $line = $this->line_sku->line;
        $positions = $line->positions;
        $employees = $this->employees;

        static $presentPositions = null;
        if (is_null($presentPositions)) {
            $presentCodes = BiometricTransaction::whereDate('event_time', $today)
                ->pluck('pin')
                ->toArray();
        }

        $filteredEmployees = $employees->filter(function ($employee) use ($presentCodes) {
            return !in_array($employee->code, $presentCodes);
        });

        $unassignedPositions = $positions->filter(function ($position) use ($employees) {
            return $employees->contains('position', $position->name);
        });

        $lastTask = TaskProductionPlan::where('line_id', $this->line_id)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->latest('end_date')
            ->first();

        return [
            'id' => strval($this->id),
            'line' => $line->code,
            'operation_date' => $this->operation_date,
            'start_date' => $this->start_date,
            'assigned_employees' => $employees->count(),
            'flag' => $employees->count() < $positions->count(),
            'total_lbs' => $this->total_lbs,
            'sku' => new SKUResource($this->line_sku->sku),
            'filtered_employees' => TaskProductionEmployeeResource::collection($filteredEmployees),
            'all_employees' => TaskProductionEmployeeResource::collection($employees),
            'exists_previuos_config' => $lastTask !== null,
            'positions' => PositionResource::collection($unassignedPositions)
        ];
    }
}
