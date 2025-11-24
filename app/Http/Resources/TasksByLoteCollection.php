<?php

namespace App\Http\Resources;

use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TasksByLoteCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lotes = Lote::all();
        return [
            'statusCode' => 200,
            'data' => $this->collection->groupBy('cdp.lote_id')->map(function ($tasks, $lote_id) use ($lotes) {
                $lote = $lotes->firstWhere('id', $lote_id);
                return [
                    'lote' => $lote->name,
                    'lote_id' => $lote_id,
                    'total_budget' => round($tasks->sum('budget'), 2),
                    'total_workers' => $tasks->sum('workers_quantity'),
                    'total_hours' => round($tasks->sum('hours'), 2),
                    'total_tasks' => $tasks->count(),
                    'finished_tasks' => $tasks->filter(function ($task) {
                        return !is_null($task->end_date);
                    })->count(),
                ];
            })->values()
        ];
    }
}
