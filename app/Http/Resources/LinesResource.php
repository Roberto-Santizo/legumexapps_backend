<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shift = $this->shift ? 'AM' : 'PM';
        return [
            'id' => strval($this->id),
            'code' => $this->code,
            'total_persons' => $this->total_persons,
            'shift' => $shift,
            'name' => $this->name,
            'positions' => PositionResource::collection($this->positions)
        ];
    }
}
