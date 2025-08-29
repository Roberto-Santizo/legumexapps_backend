<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiometricEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => strval($this['temp_id']),
            'name' => $this['name'],
            'code' => $this['code'],
            'position' => $this['position'],
            'flag' => true
        ];
    }
}
