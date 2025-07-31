<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskProductionOperationDateResource;
use App\Http\Resources\TaskProductionPlanNoOperationDateResource;
use App\Http\Resources\TaskProductionPlanByLineResource;
use App\Http\Resources\TaskProductionPlanSummaryResource;
use App\Http\Resources\WeeklyPlanProductionResource;
use App\Imports\CreateAssignmentsProductionImport;
use App\Imports\WeeklyProductionPlanImport;
use App\Models\Line;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyProductionPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WeeklyProductionPlanController extends Controller
{
    public function index(Request $request)
    {

        if ($request->query('paginated')) {
            $plans_production = WeeklyProductionPlan::orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $plans_production = WeeklyProductionPlan::get();
        }

        return WeeklyPlanProductionResource::collection($plans_production);
    }

    public function show(string $id)
    {
        $weekly_plan = WeeklyProductionPlan::find($id);

        $groupedTasks = $weekly_plan->tasks->groupBy(function ($task) {
            return $task->line->code;
        });

        $lineas = $groupedTasks->keys()->map(function ($linea) {
            $line = Line::where('code', $linea)->first();
            $tasks = TaskProductionPlan::where('line_id', $line->id)->get();

            $allCompleted = $tasks->every(fn($task) => $task->employees->count() > 0);

            return [
                'id' => strval($line->id),
                'line' => $line->name,
                'status' => true,
                'total_employees' => $line->positions->count(),
                'assigned_employees' => $tasks->first()->employees->count(),
            ];
        });

        return response()->json([
            'data' => $lineas
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            $import = new WeeklyProductionPlanImport();

            Excel::import($import, $request->file('file'));

            if (!empty($import->errors)) {
                return response()->json([
                    'msg' => 'Plan creado con advertencias',
                    'plan_errors' => $import->errors
                ], 400);
            }

            return response()->json('Plan Creado Correctamente', 200);
        } catch (\Throwable  $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }


    public function createAssigments(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new CreateAssignmentsProductionImport($id), $request->file('file'));

            return response()->json('Asignaciones Cargadas Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetTasksByLineId(string $weekly_plan_id, string $line_id)
    {
        $weekly_plan = WeeklyProductionPlan::find($weekly_plan_id);

        if (!$weekly_plan) {
            return response()->json(['msg' => 'Plan Semanal Not Found'], 404);
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $tasks = $weekly_plan->tasks()
            ->with([
                'timeouts',
                'employees',
                'line_sku.line',
                'line_sku.sku'
            ])
            ->where('line_id', $line_id)
            ->whereNot('status', 0)
            ->where(function ($query) use ($today, $yesterday) {
                $query->whereDate('operation_date', $today)
                    ->orWhere(function ($q) use ($yesterday) {
                        $q->whereDate('operation_date', $yesterday)
                            ->whereHas('line', function ($q2) {
                                $q2->where('shift', 0);
                            });
                    });
            })
            ->orderBy('operation_date', 'DESC')
            ->get();

        return TaskProductionPlanByLineResource::collection($tasks);
    }



    public function GetTasksByDate(Request $request, string $weekly_plan_id)
    {
        $date = Carbon::parse($request->query('date'));

        $weekly_plan = WeeklyProductionPlan::find($weekly_plan_id);

        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'Plan Semanal Not Found'
            ], 404);
        }

        $tasks = $weekly_plan->tasks()->orderBy('priority', 'ASC')
            ->whereDate('operation_date', $date)
            ->get();

        $newTasks = TaskProductionPlanSummaryResource::collection($tasks);

        $summary = $tasks->groupBy('line')->map(function ($group) {
            return [
                'id' => strval($group->first()->line->id),
                'line' => $group->first()->line->name,
                'total_hours' => round($group->sum(fn($task) => $task->total_hours ?? 0), 2)
            ];
        })->values();

        return response()->json([
            'summary' => $summary,
            'data' => $newTasks
        ], 200);
    }


    public function GetHoursByDate(string $weekly_plan_id)
    {
        $weekly_plan = WeeklyProductionPlan::find($weekly_plan_id);
        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'Plan Semanal Not Found'
            ], 404);
        }

        $startOfWeek = Carbon::now()
            ->setISODate($weekly_plan->year, $weekly_plan->week)
            ->startOfWeek();

        $groupedTasks = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $tasks_by_line = $weekly_plan->tasks()->whereDate('operation_date', $date)->get()->groupBy('line_id');

            foreach ($tasks_by_line as $line_id => $tasks) {
                $groupedTasks[] = [
                    'date' => $date,
                    'line_id' => strval($line_id),
                    'total_hours' => $tasks->sum('total_hours')
                ];
            }
        }

        return response()->json([
            'data' => $groupedTasks
        ], 200);
    }

    public function GetTasksNoOperationDate(Request $request, string $id)
    {
        $weekly_plan = WeeklyProductionPlan::find($id);

        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'Plan Semanal No Encontrado'
            ], 404);
        }

        try {
            $query = TaskProductionPlan::query();
            $query->where('weekly_production_plan_id', $id);
            $query->whereNull('operation_date');

            if ($request->query('line')) {
                $query->where('line_id', $request->query('line'));
            }

            if ($request->query('product')) {
                $query->whereHas('line_sku', function ($q) use ($request) {
                    $q->whereHas('sku', function ($q2) use ($request) {
                        $q2->where('product_name', 'LIKE', '%' . $request->query('product') . '%');
                    });
                });
            }

            if ($request->query('sku')) {
                $query->whereHas('line_sku', function ($q) use ($request) {
                    $q->whereHas('sku', function ($q2) use ($request) {
                        $q2->where('code', 'LIKE', '%' . $request->query('sku') . '%');
                    });
                });
            }


            return response()->json(TaskProductionPlanNoOperationDateResource::collection($query->get()));
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetTasksOperationDate(Request $request, string $weekly_plan_id)
    {
        $date = Carbon::parse($request->query('date'));

        try {
            $query = TaskProductionPlan::query();
            $query->where('weekly_production_plan_id', $weekly_plan_id);
            $query->whereDate('operation_date', $date);

            if ($request->query('line')) {
                $query->whereHas('line', function ($q) use ($request) {
                    $q->where('code', $request->query('line'));
                });
            }

            if ($request->query('sku')) {
                $query->whereHas('line_sku', function ($q) use ($request) {
                    $q->whereHas('sku', function ($q2) use ($request) {
                        $q2->where('code', 'LIKE', '%' . $request->query('sku') . '%');
                    });
                });
            }

            if ($request->query('status')) {
                $query->where('status', $request->query('status'));
            }

            return TaskProductionOperationDateResource::collection($query->get());
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetTasksForCalendar(Request $request, string $id)
    {

        $weekly_plan = WeeklyProductionPlan::find($id);

        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'Plan Semanal No Encontrado'
            ], 404);
        }

        try {
            $tasks = TaskProductionPlan::where('weekly_production_plan_id', $id)
                ->with('line_sku', 'line')
                ->whereNotNull('operation_date')
                ->get();

            $events = $tasks
                ->groupBy('operation_date')
                ->flatMap(function (Collection $tasksByDate, $dateKey) {
                    $groupedByLine = $tasksByDate->groupBy(fn($task) => $task->line->code);
                    $date = Carbon::parse($dateKey);

                    return $groupedByLine->map(function ($tasks, $lineName) use ($date) {
                        $hours = $tasks->sum(function ($task) {
                            return $task->total_lbs / $task->line_sku->lbs_performance;
                        });

                        return [
                            'id' => uniqid(),
                            'title' => "{$lineName} | " . round($hours, 2) . " h",
                            'start' => $date->format('Y-m-d'),
                        ];
                    })->values();
                })->values()->all();

            return response()->json($events);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
