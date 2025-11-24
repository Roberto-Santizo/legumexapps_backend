<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskWeeklyPlanDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $diff_hours = 0;
        $passed_hours = (!$this->end_date && $this->start_date) ? $this->start_date->diffInHours(Carbon::now()) : null;

        if($this->closures->count() > 0){
            foreach ($this->closures as $closure) {
                $diff_hours += $closure->start_date->diffInHours($closure->end_date);
            }
        }

        return [
            'task' => $this->task->name,
            'lote' => $this->cdp ? $this->cdp->lote->name : '',
            'week' => $this->plan->week,
            'finca' => $this->plan->finca->name,
            'start_date' => $this->start_date ? $this->start_date->format('d-m-Y h:i:s A'): null,
            'end_date' => $this->end_date ? $this->end_date->format('d-m-Y h:i:s A'): null,
            'hours' => $this->hours,
            'real_hours' => $this->end_date ? round(($this->start_date->diffInHours($this->end_date) - $diff_hours),2)  : null,
            'slots' => $this->slots,
            'total_employees' => $this->employees->count(),
            'employees' => $this->employees->map(function($employee){
                return [
                    'name' => $employee->name,
                    'code' => $employee->code
                ];
            }),
            'closures' => $this->closures->map(function($closure){
                return [
                    'start_date' => $closure->start_date ? $closure->start_date->format('d-m-Y h:i:s A') : null,
                    'end_date' => $closure->end_date ?  $closure->end_date->format('d-m-Y h:i:s A') : null
                ];
            }),
            'insumos' => $this->insumos ? TaskInsumosResource::collection($this->insumos) : [],
            'use_dron' => $this->use_dron ? true : false,
            'passed_hours' => $passed_hours ? round(($passed_hours  - $diff_hours),2) : null
        ];
    }
}
