<?php

namespace App\Http\Controllers;

use App\Http\Resources\TasksNoOperationDateResource;
use App\Http\Resources\TasksWeeklyPlanForCalendarResource;
use Exception;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use App\Imports\WeeklyPlanImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\WeeklyPlanCollection;
use App\Models\LotePlantationControl;
use App\Models\TaskWeeklyPlan;
use Carbon\Carbon;

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
            })->where('year', $year)->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $query->orderBy('created_at', 'DESC')->paginate(10);
        }

        return new WeeklyPlanCollection($query->paginate(10));
    }



    public function GetAllPlans()
    {
        return new WeeklyPlanCollection(WeeklyPlan::all());
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

            return response()->json([
                'message' => 'Plan Creado Correctamente'
            ]);
        } catch (\Throwable  $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $plan = WeeklyPlan::find($id);
        if (!$plan) {
            return response()->json([
                'msg' => ['El plan no existe']
            ], 404);
        }

        $today = Carbon::today();
        $role = $request->user()->getRoleNames()->first();

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
        $weekly_plan = WeeklyPlan::find($id);
        if (!$weekly_plan) {
            return response()->json([
                'errors' => 'El plan no existe'
            ], 404);
        }

        $query = TaskWeeklyPlan::query();
        $query->where('weekly_plan_id', $id);

        if ($request->query('lote')) {
            $query->whereHas('lotePlantationControl', function ($query) {
                $query->where('lote_id', request()->query('lote'));
            });
        }

        if ($request->query('task')) {
            $query->where('tarea_id', request()->query('task'));
        }

        $query->where('operation_date', null);


        return TasksNoOperationDateResource::collection($query->get());
    }

    public function GetTasksForCalendar(string $id)
    {
        $weekly_plan = WeeklyPlan::find($id);
        if (!$weekly_plan) {
            return response()->json([
                'errors' => 'El plan no existe'
            ], 404);
        }


        $initial_date = Carbon::now()->setISODate($weekly_plan->year, $weekly_plan->week)->startOfWeek();
        $tasks_with_operation_date = $weekly_plan->tasks()->whereNot('operation_date', null)->get()->count();
        $tasks_without_operation_date = $weekly_plan->tasks()->where('operation_date', null)->get()->count();
        $tasks = TasksWeeklyPlanForCalendarResource::collection($weekly_plan->tasks()->whereNot('operation_date', null)->get());
        return response()->json([
            'data' => $tasks,
            'initial_date' => $initial_date->format('Y-m-d'),
            'tasks_with_operation_date' => $tasks_with_operation_date,
            'tasks_without_operation_date' => $tasks_without_operation_date,
        ]);
    }
}
