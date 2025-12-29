<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskWeeklyPlanInProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assigned_employees = $this->employees->count();
        $paused = $this->closures->last();
        $has_insumos = $this->insumos->count() > 0 ? true : false;
        return [
            'id' => strval($this->id),
            'task' => $this->task->name,
            'finca' => $this->plan->finca->name,
            'lote' => $this->cdp->lote->name,
            'week' => $this->plan->week,
            'assigned_employees' => $assigned_employees,
            'total_employees' => $this->workers_quantity,
            'paused' => ($paused && !$paused->end_date) ? true : false,
            'has_insumos' => $has_insumos
        ];
    }
}
