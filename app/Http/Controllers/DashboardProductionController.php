<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskProductionDashboardResource;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyProductionPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardProductionController extends Controller
{
    public function GetFinishedTasksPerLine()
    {
        try {
            $week = Carbon::now()->weekOfYear;

            $plan = WeeklyProductionPlan::where('week', $week)->first();

            if (!$plan) {
                return response()->json(['msg' => 'No plan found for this week'], 404);
            }

            $tasks = TaskProductionPlan::select(['line_id', DB::raw('COUNT(*) as total_tasks'), DB::raw('SUM(CASE WHEN end_date IS NOT NULL THEN 1 ELSE 0 END) as finished_tasks')])
                ->where('weekly_production_plan_id', $plan->id)
                ->with('line:id,name')
                ->groupBy('line_id')
                ->get();

            $data = $tasks->map(function ($task) {
                return [
                    'line' => optional($task->line)->name ?? 'Sin línea',
                    'finished_tasks' => (int)$task->finished_tasks,
                    'total_tasks' => (int)$task->total_tasks,
                    'percentage' => $task->finished_tasks / $task->total_tasks
                ];
            });

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function GetInProgressTasks()
    {
        try {
            $week = Carbon::now()->weekOfYear;

            $plan = WeeklyProductionPlan::where('week', $week)->first();

            if (!$plan) {
                return response()->json(['msg' => 'No plan found for this week'], 404);
            }

            $tasks = $plan->tasks()->select()->whereNotNull('start_date')->whereNull('end_date')->get();

            return TaskProductionDashboardResource::collection($tasks);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 500);
        }
    }
}
