<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeTaskCropResource;
use App\Http\Resources\TaskCropResource;
use App\Models\DailyAssignments;
use App\Models\EmployeeTaskCrop;
use App\Models\TaskCrop;
use App\Models\TaskCropWeeklyPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TasksCropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'lote_plantation_control_id' => 'required|string',
            'weekly_plan_id' => 'required|string'
        ]);

        $tasks = TaskCropWeeklyPlan::where('lote_plantation_control_id', $data['lote_plantation_control_id'])->where('weekly_plan_id', $data['weekly_plan_id'])->get();
        return [
            'week' => $tasks->first()->plan->week,
            'finca' => $tasks->first()->plan->finca->name,
            'lote' => $tasks->first()->lotePlantationControl->lote->name,
            'tasks' =>   TaskCropResource::collection($tasks),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = TaskCropWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'error' => "Task not found"
            ], 404);
        }

        return response()->json([
            'data' =>  new TaskCropResource($task)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function CloseAssigment(Request $request, string $id)
    {
        $data = $request->input('data');
        $task = TaskCropWeeklyPlan::find($id);

        foreach ($data as $item) {
            EmployeeTaskCrop::create([
                'task_crop_weekly_plan_id' => $task->id,
                'employee_id' => $item['emp_id'],
                'code' => $item['code'],
                'name' => $item['name'],
            ]);
        }

        DailyAssignments::create([
            'task_crop_weekly_plan_id' => $task->id,
            'start_date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Assignment closed'
        ]);
    }

    public function EmployeesAssignment(string $id)
    {
        $task = TaskCropWeeklyPlan::find($id);

        return response()->json([
            'task' => $task->task->name,
            'week' => $task->plan->week,
            'finca' => $task->plan->finca->name,
            'date_assignment' => $task->assignment_today->start_date,
            'data' => EmployeeTaskCropResource::collection($task->employees)
        ]);
    }
}
