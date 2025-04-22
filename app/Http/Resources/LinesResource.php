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
            'shift' => $shift,
            'name' => $this->name,
            'positions' => PositionResource::collection($this->positions)
        ];
    }
}
