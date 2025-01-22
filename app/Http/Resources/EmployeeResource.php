<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'emp_id' => strval($this->emp_id),
            'name' => $this->empleado->first_name,
            'code' => $this->empleado->last_name,
        ];
    }
}
