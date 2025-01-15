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

        //TAREAS
        $total_budget = $this->tasks->sum('budget');
        $used_budget = $this->tasks()->whereNot('end_date')->sum('budget');

        //TAREAS EXTRAORDINARIAS
        $total_budget_ext = $this->tasks()->where('extraordinary', 0)->sum('budget');
        $used_total_budget_ext = $this->tasks()->where('extraordinary', 0)->whereNot('end_date')->sum('budget');

        //TASKS
        $total_tasks = $this->tasks->count();
        $finished_total_tasks = $this->tasks()->whereNot('end_date')->count();
        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'finca' => $this->finca->name,
            'created_at' => $this->created_at->format('d-m-Y'),
            'total_budget' => $total_budget,
            'used_budget' => $used_budget,
            'total_budget_ext' => $total_budget_ext,
            'used_total_budget_ext' => $used_total_budget_ext,
            'total_tasks' => $total_tasks,
            'finished_total_tasks' => $finished_total_tasks,
            'tasks_crop' => '0/0',
        ];
    }
}
