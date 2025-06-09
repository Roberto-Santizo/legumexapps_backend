<?php

namespace App\Http\Controllers;

use App\Http\Resources\TasksNoOperationDateResource;
use App\Http\Resources\TasksWeeklyPlanForCalendarResource;
use App\Http\Resources\TaskWeeklyPlanByDateResource;
use Exception;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use App\Imports\WeeklyPlanImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\WeeklyPlanCollection;
use App\Http\Resources\WeeklyPlanResource;
use App\Models\LotePlantationControl;
use App\Models\TaskWeeklyPlan;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class WeeklyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $week = Carbon::now()->weekOfYear;
        $year = Carbon::now()->year;

        $role = $request->user()->getRoleNames();
        $adminroles = ['admin', 'adminagricola', 'auxrrhh'];

        $query = WeeklyPlan::query();

        if ($request->query('week')) {
            $query->where('week', $request->query('week'));
        }

        if ($request->query('year')) {
            $query->where('year', $request->query('year'));
        }

        if ($request->query('finca_id')) {
            $query->where('finca_id', $request->query('finca_id'));
        }

        if (!in_array($role[0], $adminroles)) {
            $permission = $request->user()->getRoleNames()->first();
            $query->whereHas('finca', function ($query) use ($permission) {
                $query->where('name', 'LIKE', '%' . $permission . '%');
            })->where(function ($query) use ($week) {
                $query->where('week', $week)->orWhere('week', $week - 1);
            })->where('year', $year)->orderBy('created_at', 'DESC');
        } else {
            $query->orderBy('created_at', 'DESC');
        }

        if ($request->query('paginated')) {
            return WeeklyPlanResource::collection($query->paginate(10));
        } else {
            return WeeklyPlanResource::collection($query->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new WeeklyPlanImport, $request->file('file'));

            return response()->json('Plan Creado Correctamente', 200);
        } catch (\Throwable  $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = WeeklyPlan::find($id);
        $payload = JWTAuth::getPayload();

        if (!$plan) {
            return response()->json([
                'msg' => ['El plan no existe']
            ], 404);
        }

        $today = Carbon::today();
        $role = $payload->get('role');

        if ($role != 'admin' && $role != 'adminagricola') {
            $tasks_by_lote = $plan->tasks()
                ->where(function ($query) use ($today) {
                    $query->whereDate('operation_date', $today)
                        ->orWhereHas('closures', function ($q) {
                            $q->where('end_date', null);
                        });
                })
                ->orderBy('lote_plantation_control_id', 'ASC')
                ->get()
                ->groupBy('lote_plantation_control_id');
        } else {
            $tasks_by_lote = $plan->tasks->groupBy('lote_plantation_control_id');
        }

        $tasks_crop_by_lote = $plan->tasks_crops->groupBy('lote_plantation_control_id');

        $summary_tasks = $tasks_by_lote->map(function ($group, $key) {
            return [
                'lote' => LotePlantationControl::find($key)->lote->name,
                'lote_plantation_control_id' => strval($key),
                'total_budget' => round($group->sum('budget'), 2),
                'total_workers' => $group->sum('workers_quantity'),
                'total_hours' => round($group->sum('hours'), 2),
                'total_tasks' => $group->count(),
                'finished_tasks' => $group->filter(function ($task) {
                    return !is_null($task->end_date);
                })->count(),
            ];
        })->values();

        $summary_crops = $tasks_crop_by_lote->map(function ($group, $key) {
            $lote_plantation_control = LotePlantationControl::find($key);
            return [
                'id' => strval($key),
                'lote_plantation_control_id' => strval($lote_plantation_control->id),
                'lote' => $lote_plantation_control->lote->name,
            ];
        })->values();

        return response()->json([
            'data' => [
                'id' => strval($plan->id),
                'finca' => $plan->finca->name,
                'week' => $plan->week,
                'year' => $plan->year,
                'summary_tasks' => $summary_tasks,
                'summary_crops' => $summary_crops
            ]
        ]);
    }

    public function GetTasksWithNoPlanificationDate(Request $request, string $id)
    {
        $query = WeeklyPlan::query();
        $query->where('id', $id);

        $weekly_plan = $query->get();

        if ($weekly_plan->isEmpty()) {
            return response()->json([
                'msg' => 'El plan no existe'
            ], 404);
        }

        $all_tasks = $weekly_plan->flatMap(function ($plan) {
            $tasks = $plan->tasks()->where('operation_date', null)->get();
            return $tasks;
        });

        if ($request->query('lote')) {
            $all_tasks = $all_tasks->filter(function ($task) use ($request) {
                return $task->lotePlantationControl && $task->lotePlantationControl->lote_id == $request->query('lote');
            });
        }

        if ($request->query('finca')) {
            $all_tasks = $all_tasks->filter(function ($task) use ($request) {
                return $task->plan && $task->plan->finca->id == $request->query('finca');
            });
        }

        if ($request->query('task')) {
            $all_tasks = $all_tasks->where('tarea_id', $request->query('task'));
        }


        return TasksNoOperationDateResource::collection($all_tasks);
    }

    public function GetTasksForCalendar(string $id)
    {
        $query = WeeklyPlan::query();
        $query->where('id', $id);
        $adminroles = ['admin', 'adminagricola'];

        $payload = JWTAuth::getPayload();
        $role = $payload->get('role');

        if (!in_array($role[0], $adminroles)) {
            $query->whereHas('finca', function ($q) use ($role) {
                $q->where('name', 'like', '%' . $role[0] . '%');
            });
        }

        $weekly_plan = $query->get();

        if ($weekly_plan->isEmpty()) {
            return response()->json([
                'msg' => 'No se encontraron datos de la semana actual',
            ], 404);
        }


        $initial_date = Carbon::now()->setISODate($weekly_plan->first()->year, $weekly_plan->first()->week)->startOfWeek();

        $all_tasks = $weekly_plan->flatMap(function ($plan) {
            $tasks = $plan->tasks()->whereNot('operation_date', null)->get();
            return $tasks;
        });

        $tasks_with_operation_date = $all_tasks->whereNotNull('operation_date')->count();
        $tasks_without_operation_date = $all_tasks->whereNull('operation_date')->count();

        $tasks = TasksWeeklyPlanForCalendarResource::collection($all_tasks);


        return response()->json([
            'data' => $tasks,
            'initial_date' => $initial_date->format('Y-m-d'),
            'tasks_with_operation_date' => $tasks_with_operation_date,
            'tasks_without_operation_date' => $tasks_without_operation_date,
        ]);
    }

    public function GetTasksPlannedByDate(Request $request)
    {
        $query = WeeklyPlan::query();
        if ($request->query('weekly_plan')) {
            $query->where('id', $request->query('weekly_plan'));
        } else {
            $week = Carbon::now()->weekOfYear;
            $year = Carbon::now()->year;
            $query->where('week', $week)->where('year', $year);
        }

        $weekly_plan = $query->first();
        $tasks = TaskWeeklyPlan::query();

        $tasks->where('weekly_plan_id', $weekly_plan->id);

        if ($request->query('lote')) {
            $tasks->whereHas('lotePlantationControl', function ($query) use ($request) {
                $query->where('lote_id', $request->query('lote'));
            });
        }

        if ($request->query('task')) {
            $tasks->where('tarea_id', $request->query('task'));
        }

        $tasks->whereDate('operation_date', $request->query('date'));

        $tasks->whereHas('insumos');
        return TaskWeeklyPlanByDateResource::collection($tasks->get());
    }
}
