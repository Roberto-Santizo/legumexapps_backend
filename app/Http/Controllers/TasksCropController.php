<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeTaskCropResource;
use App\Http\Resources\TaskCropIncomplemeteAssignmentResource;
use App\Http\Resources\TaskCropResource;
use App\Models\DailyAssignments;
use App\Models\EmployeeTaskCrop;
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

    public function CloseDailyAssigment(Request $request, string $id)
    {
        $data = $request->validate([
            'task_crop_id' => 'required',
            'plants' => 'required|numeric',
            'assigments' => 'required',
            'lbs_finca' => 'required|numeric'
        ]);

        $task = TaskCropWeeklyPlan::find($id);
        $task_crop_daily_assigment = $task->assignment_today;
        $assigments = $task->employees;
        foreach ($data['assigments'] as $assigment) {
            foreach ($assigments as $assigment_crop) {
                if ($assigment_crop->id === (int)$assigment['id']) {
                    $assigment_crop->lbs = $assigment['lbs'];
                    $assigment_crop->save();
                }
            }
        }
        $task_crop_daily_assigment->end_date = Carbon::now();
        $task_crop_daily_assigment->plants = $data['plants'];
        $task_crop_daily_assigment->lbs_finca = $data['lbs_finca'];
        $task_crop_daily_assigment->save();

        return response()->json([
            'message' => 'Daily Assigment Closed'
        ]);
    }

    public function GetIncompleteAssignments(string $id)
    {
        $task = TaskCropWeeklyPlan::find($id);

        return response()->json([
            'data' => TaskCropIncomplemeteAssignmentResource::collection($task->assigments()->where('lbs_planta', null)->orderBy('start_date')->get())
        ]);
    }

    public function RegisterDailyAssigment(Request $request)
    {

        foreach ($request->all() as $data) {
            $task = DailyAssignments::find($data['id']);
            $task->lbs_planta = $data['lbs_planta'];
            $task->save();
        }


        return response()->json([
            'message' => 'Task Closed Successfully'
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
