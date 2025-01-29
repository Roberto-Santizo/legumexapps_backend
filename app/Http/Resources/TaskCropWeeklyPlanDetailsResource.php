<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCropWeeklyPlanDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assigments = $this->assigments()->whereNot('end_date',null)->whereNot('lbs_planta',null)->get();
        return DailyAssignmentResource::collection($assigments)->toArray($request);
    }
}
