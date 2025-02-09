<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeesResource;
use App\Http\Resources\FinishedTaskCropResource;
use App\Http\Resources\FinishedTasksWeeklyPlanResource;
use App\Http\Resources\PlanFincaFinishedTasksResource;
use App\Http\Resources\TasksCropWeeklyPlanInProgressResource;
use App\Http\Resources\TaskWeeklyPlanInProgressResource;
use App\Models\DailyAssignments;
use App\Models\EmployeeTask;
use App\Models\PersonnelEmployee;
use App\Models\TaskCropWeeklyPlan;
use App\Models\TaskWeeklyPlan;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardAgricolaController extends Controller
{
    public function GetDronHours(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($request->has('permission') && !in_array($request->input('permission'), ['admin', 'adminagricola'])) {
            $tasks_dron = TaskWeeklyPlan::where('use_dron', 1)->whereHas('plan.finca', function ($query) use ($week, $year,$request) {
                $query->where('name', 'LIKE', '%' . $request->input('permission') . '%');
            })->whereHas('plan',function($query) use($week,$year){
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
        }else{
            $tasks_dron = TaskWeeklyPlan::where('use_dron', 1)->whereHas('plan', function ($query) use ($week, $year) {
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
        }

        $hours = 0;
        foreach ($tasks_dron as $task) {
            $hours += $task->start_date->diffInHours($task->end_date);
        }

        return response()->json([
            'hours' => round($hours, 4)
        ]);
    }

    public function GetSummaryHoursEmployees(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;
        $employees = EmployeesResource::collection(PersonnelEmployee::all());

        $format_employees = $employees->map(function ($employee) use ($week, $year) {
            $flag = false;
            $assignments = EmployeeTask::where('code', $employee->last_name)->whereHas('task_weekly_plan.plan', function ($query) use ($week, $year) {
                $query->where('week', $week);
                $query->where('year', $year);
            })->get();
            $weekly_hours = 0;
            if ($assignments->count()) {
                foreach ($assignments as $assignment) {
                    if (!$assignment->task_weekly_plan->end_date) {
                        $flag = true;
                    }
                    $weekly_hours += ($assignment->task_weekly_plan->hours / $assignment->task_weekly_plan->employees->count());
                }
            }
            $employee->weekly_hours = $weekly_hours;
            $employee->assigned = $flag;
            return $employee;
        });


        $sorted_employees = $format_employees->sortByDesc('weekly_hours')->values();
        return response()->json([
            'data' => EmployeesResource::collection($sorted_employees)
        ]);
    }

    public function GetTasksInProgress(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($request->has('permission') && !in_array($request->input('permission'), ['admin', 'adminagricola'])) {
            $tasks = TaskWeeklyPlan::whereHas('plan.finca', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('permission') . '%');
            })->whereHas('plan',function($query) use($week,$year){
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->whereDate('start_date', Carbon::today())->where('end_date', null)->get();
        }else{
            $tasks = TaskWeeklyPlan::whereHas('plan', function ($query) use ($week, $year) {
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->whereDate('start_date', Carbon::today())->where('end_date', null)->get();
        }
        

        return TaskWeeklyPlanInProgressResource::collection($tasks);
    }

    public function GetFinishedTasks(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        
        if ($request->has('permission') && !in_array($request->input('permission'), ['admin', 'adminagricola'])) {
            $tasks = TaskWeeklyPlan::whereHas('plan.finca', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('permission') . '%');
            })->whereHas('plan',function($query) use($week,$year){
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->whereNot('end_date', null)->get();
        }else{
            $tasks = TaskWeeklyPlan::whereHas('plan', function ($query) use ($week, $year) {
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->whereNot('end_date', null)->get();
        }
        

        return FinishedTasksWeeklyPlanResource::collection($tasks);
    }

    public function GetTasksCropInProgress(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($request->has('permission') && !in_array($request->input('permission'), ['admin', 'adminagricola'])) {
            $tasks = DailyAssignments::where('end_date', null)->whereDate('start_date', Carbon::today())->whereHas('TaskCropWeeklyPlan.plan.finca', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('permission') . '%');
            })->whereHas('TaskCropWeeklyPlan.plan',function($query) use($week,$year){
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
    
        }else{
            $tasks = DailyAssignments::where('end_date', null)->whereDate('start_date', Carbon::today())->whereHas('TaskCropWeeklyPlan.plan', function ($query) use ($week, $year) {
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
    
        }
       
        return TasksCropWeeklyPlanInProgressResource::collection($tasks);
    }

    public function GetFinishedTasksCrop(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($request->has('permission') && !in_array($request->input('permission'), ['admin', 'adminagricola'])) {
            $tasks = DailyAssignments::whereNot('end_date', null)->whereDate('start_date', Carbon::today())->whereHas('TaskCropWeeklyPlan.plan.finca', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('permission') . '%');
            })->whereHas('TaskCropWeeklyPlan.plan',function($query) use($week,$year){
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
    
        }else{
            $tasks = DailyAssignments::whereNot('end_date', null)->whereDate('start_date', Carbon::today())->whereHas('TaskCropWeeklyPlan.plan', function ($query) use ($week, $year) {
                $query->where('week',$week)->OrWhere('week',$week)->where('year',$year);
            })->get();
    
        }

        return FinishedTaskCropResource::collection($tasks);
    }

    public function GetFinishedTasksByFinca(Request $request)
    {
        $week = $request->input('week') ?? Carbon::now()->weekOfYear;
        $year = $request->input('year') ?? Carbon::now()->year;

        $plans = WeeklyPlan::where('year',$year)->where('week',$week)->with('finca')->get();
        return PlanFincaFinishedTasksResource::collection($plans);
    }
}
