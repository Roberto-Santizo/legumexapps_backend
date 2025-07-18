<?php

namespace App\Http\Resources;

use App\Models\TaskProductionEmployeesBitacora;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTaskProductionDetailResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'position' => $this->position,
            'bitacoras' => TaskProductionEmployeeBitacoraResource::collection($this->bitacoras)
        ];
    }
}
