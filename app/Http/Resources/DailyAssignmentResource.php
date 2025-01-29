<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lbs_planta' => $this->lbs_planta,
            'lbs_finca' => $this->lbs_finca,
            'start_hour' => $this->start_date->format('h:i:s A'),
            'end_hour' => $this->end_date->format('h:i:s A'),
            'date' => $this->end_date,
            'plants' => $this->plants
        ];
    }
}

// export const TaskCropWeeklyPlanDetailSchema = z.object({
//     finca: z.string(),
//     week: z.number(),
//     lote: z.string(),
//     cdp: z.string(),
//     assigments: z.array(z.object({
//         id: z.number(),
//         lbs_planta: z.number(),
//         lbs_finca: z.number(),
//         plants: z.number(),
//         start_hour: z.string(),
//         end_hour: z.string(),
//         date: z.string()
//     }))
// });
