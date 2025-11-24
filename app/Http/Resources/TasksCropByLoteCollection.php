<?php

namespace App\Http\Resources;

use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TasksCropByLoteCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'statusCode' => 200,
            'data' => TasksCropByLoteResource::collection($this->collection),
        ];
    }
}
