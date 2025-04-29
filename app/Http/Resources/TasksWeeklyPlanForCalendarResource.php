<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksWeeklyPlanForCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $permissions = $request->user()->permissions()->get()->pluck('name')->toArray();
        
        $colors = [
            '1' => 'red',
            '2' => 'orange',
            '3' => 'green',
        ];

        $color = match (true) {
            !$this->start_date => '1',
            $this->start_date && !$this->end_date => '2',
            default => '3'
        };

        $flag = ($color !== '3' && $color !== '2');

        return [
            'id' => strval($this->id),
            'title' => $this->task->name . ' - ' . $this->plan->finca->name . ' - ' . $this->lotePlantationControl->lote->name,
            'start' => $this->operation_date->format('Y-m-d'),
            'backgroundColor' => $colors[$color],
            'editable' => in_array('edit fincas planification',$permissions) ? $flag : false,
            'task' => $this->task->name,
            'finca' => $this->plan->finca->name,
            'lote' => $this->lotePlantationControl->lote->name,
            'cdp' => $this->lotePlantationControl->cdp->name,
            'end' => $this->end_date ? $this->end_date->addDay()->format('Y-m-d') :  '',
        ];
    }
}
