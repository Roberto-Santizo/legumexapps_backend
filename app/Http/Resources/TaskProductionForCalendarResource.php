<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionForCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
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
            'title' => $this->id . ' - ' . $this->line_sku->line->code . ' - ' . $this->line_sku->sku->code,
            'start' => $this->operation_date ? $this->operation_date->format('Y-m-d') : null,
            'total_hours' => $this->total_hours ?? 0,
            'priority' => strval($this->priority),
            'line_id' => strval($this->line_id),
            'backgroundColor' => $colors[$color],
            'editable' => $flag
        ];
    }
}
