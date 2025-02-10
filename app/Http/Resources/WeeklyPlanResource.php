<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $total_budget = $this->tasks->sum('budget');
        $used_budget = $this->tasks()->whereNot('end_date')->sum('budget');

        $total_budget_ext = $this->tasks()->where('extraordinary', 1)->sum('budget');
        $used_total_budget_ext = $this->tasks()->where('extraordinary', 1)->whereNot('end_date')->sum('budget');

        $total_tasks = $this->tasks->count();
        $finished_total_tasks = $this->tasks()->whereNot('end_date')->count();

        $total_tasks_crop = $this->tasks_crops->count();
        $finished_total_tasks_crops = $this->tasks_crops->where('status',0)->count();

        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'finca' => $this->finca->name,
            'created_at' => $this->created_at->format('d-m-Y'),
            'total_budget' => round($total_budget,2),
            'used_budget' => round($used_budget,2),
            'total_budget_ext' => round($total_budget_ext,2),
            'used_total_budget_ext' => round($used_total_budget_ext,2),
            'total_tasks' => $total_tasks,
            'finished_total_tasks' => $finished_total_tasks,
            'total_tasks_crop' => $total_tasks_crop,
            'finished_total_tasks_crops' => $finished_total_tasks_crops
        ];
    }
}
