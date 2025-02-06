<?php

namespace App\Http\Resources;

use App\Models\TaskInsumos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCDPDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $diff_hours = 0;
        $date_week = Carbon::now()->setISODate($this->plan->year, $this->plan->week)->startOfWeek();
        $current_week = Carbon::now();
        $aplication_week = (int)abs($date_week->diffInWeeks($current_week));
        
        $real_hours = $this->end_date ? round(($this->start_date->diffInHours($this->end_date) - $diff_hours), 2) : 0;
        return [
            'id' => $this->id,
            'calendar_week' => $this->plan->week,
            'task' => $this->task->name,
            'hours' => $this->hours,
            'real_hours' => $this->end_date ? round(($this->start_date->diffInHours($this->end_date) - $diff_hours), 2)  : null,
            'aplication_week' => $aplication_week,
            'performance' => $real_hours ? round(($this->hours / $real_hours) * 100, 2) : null,
            'closed' => $this->end_date ? true : false,
            'insumos' => $this->insumos ? TaskInsumosResource::collection($this->insumos) : []

        ];
    }
}
