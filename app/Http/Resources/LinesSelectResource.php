<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinesSelectResource extends JsonResource
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
            'value' => strval($this->id),
            'label' => $this->name . ' - '  .$shift,
        ];
    }
}
