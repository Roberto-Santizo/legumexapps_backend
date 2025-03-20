<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => strval($this->id),
            'line' => $this->bitacora->assignment->TaskProduction->line->code,
            'original_name' => $this->bitacora->original_name,
            'original_code' => $this->bitacora->original_code,
            'original_position' => $this->bitacora->original_position,
            'new_name' => $this->bitacora->new_name,
            'new_code' => $this->bitacora->new_code,
            'new_position' => $this->bitacora->original_position,
            'confirmed' => $this->confirmed ? true : false,
            'permission' => $this->permission ? true : false,
            'date' => $this->created_at,
        ];
    }
}
