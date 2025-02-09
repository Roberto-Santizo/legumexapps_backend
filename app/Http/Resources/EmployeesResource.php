<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->last_name,
            'first_name' => $this->first_name,
            'weekly_hours' => $this->weekly_hours,
            'assigned'  => $this->assigned
        ];
    }
}
