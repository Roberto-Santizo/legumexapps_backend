<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionPlanDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $positions = $this->line_sku->line->positions->filter(function($position){
            $assignee = $this->employees()->where('position',$position->name)->first();
            if(!$assignee){
                return $position;
            }
        });

        return [
            'id' => strval($this->id),
            'line' => $this->line_sku->line->code,
            'operation_date' => $this->operation_date,
            'flag' => $this->employees->count() < $this->line_sku->line->positions->count(),
            'total_lbs' => $this->total_lbs,
            'sku' => new SKUResource($this->line_sku->sku),
            'employees' => TaskProductionEmployeeResource::collection($this->employees),
            'positions' => PositionResource::collection($positions)
        ];
    }
}
