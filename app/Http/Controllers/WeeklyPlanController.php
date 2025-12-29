<?php

namespace App\Http\Controllers;

use App\Http\Resources\TasksByLoteCollection;
use App\Http\Resources\TasksCropByLoteCollection;
use App\Http\Resources\TasksNoOperationDateResource;
use App\Http\Resources\TasksWeeklyPlanForCalendarResource;
use App\Http\Resources\TaskWeeklyPlanByDateResource;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use App\Imports\WeeklyPlanImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\WeeklyPlanResource;
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

        $payload = JWTAuth::getPayload();
        $role = $payload->get('role');

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

        if (!in_array($role, $adminroles)) {
            $permission = $request->user()->getRoleNames()->first();
            $query->whereHas('finca', function ($query) use ($permission) {
                $query->where('name', 'LIKE', '%' . $permission . '%');
            })->orderBy('created_at', 'DESC');
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
    public function SummaryTasksLote(string $id)
    {
        $plan = WeeklyPlan::find($id);
        $payload = JWTAuth::getPayload();

        if (!$plan) {
            return response()->json([
                'statusCode' => 404,
                'msg' => 'El plan no existe'
            ], 404);
        }

        $today = Carbon::today();
        $role = $payload->get('role');
        $query = TaskWeeklyPlan::query();
        $query->where('weekly_plan_id', $plan->id);

        if ($role != 'admin' && $role != 'adminagricola') {
            $tasks_by_lote = $query->where(function ($query) use ($today) {
                $query->whereDate('operation_date', $today)->orWhereHas('closures', function ($q) {
                    $q->where('end_date', null)->orWhereHas('taskWeeklyPlan', function ($q2) {
                        $q2->whereNull('end_date');
                    });
                });
            })->orderBy('plantation_control_id', 'ASC')->with('cdp')->get();
        } else {
            $tasks_by_lote = $query->with('cdp')->get();
        }

        return new TasksByLoteCollection($tasks_by_lote);
    }

    public function SummaryTasksCrop(string $id)
    {
        $plan = WeeklyPlan::find($id);

        if (!$plan) {
            return response()->json([
                'statusCode' => 404,
                'msg' => 'El plan no existe'
            ], 404);
        }

        $tasks_crops = $plan->tasks_crops()->with('cdp')->get();
        
        return new TasksCropByLoteCollection($tasks_crops);
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

        if (!in_array($role, $adminroles)) {
            $query->whereHas('finca', function ($q) use ($role) {
                $q->where('name', 'like', '%' . $role . '%');
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
