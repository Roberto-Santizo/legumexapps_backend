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
            'title' => $this->id . ' - ' . $this->line->code . ' - ' . $this->sku->code,
            'start' => $this->operation_date->format('Y-m-d'),
            'priority' => strval($this->priority),
            'backgroundColor' => !$flag ? 'green' : 'orange',
            'editable' => $flag
        ];
    }
}
