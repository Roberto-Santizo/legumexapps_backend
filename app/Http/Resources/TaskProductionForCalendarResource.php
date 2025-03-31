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
        $flag = $this->end_date ? false : true;
        return [
            'id' => strval($this->id),
            'title' => $this->id . ' - ' . $this->line_sku->line->code . ' - ' . $this->line_sku->sku->code,
            'start' => $this->operation_date->format('Y-m-d'),
            'total_hours' => $this->total_hours ?? 0,
            'priority' => strval($this->priority),
            'line_id' => strval($this->line_id),
            'backgroundColor' => !$flag ? 'green' : 'orange',
            'editable' => $flag
        ];
    }
}
