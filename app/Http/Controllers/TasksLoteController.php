<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditTaskWeeklyPlanRequest;
use App\Http\Resources\TaskLoteResource;
use App\Http\Resources\TaskWeeklyPlanDetailsCollection;
use App\Http\Resources\TaskWeeklyPlanDetailsResource;
use App\Http\Resources\TaskWeeklyPlanResource;
use App\Models\EmployeeTask;
use App\Models\PartialClosure;
use App\Models\TaskWeeklyPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TasksLoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string',
            'weekly_plan_id' => 'required|string'
        ]);

        $tasks = TaskWeeklyPlan::where('lote_plantation_control_id', $data['id'])->where('weekly_plan_id',$data['weekly_plan_id'])->get();

        return [
            'week' => $tasks->first()->plan->week,
            'finca' => $tasks->first()->plan->finca->name,
            'lote' => $tasks->first()->lotePlantationControl->lote->name,
            'data' => TaskWeeklyPlanResource::collection($tasks),
        ];
        
    }

    public function show(string $id)
    {
        $data = TaskWeeklyPlan::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'TaskWeeklyPlan not found'
            ], 404);
        }

        return response()->json([
            'data' => new TaskWeeklyPlanResource($data)
        ]);
    }

    public function PartialClose(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        $partial = PartialClosure::create([
            'task_weekly_plan_id' => $task->id,
            'start_date' => Carbon::now(),
        ]);

        return response()->json([
            'data' => $partial
        ]);
    }

    public function PartialCloseOpen(string $id)
    {
        $task = TaskWeeklyPlan::find($id);
        
        $registro = $task->closures->last();
        $registro->update([
            'end_date' => Carbon::now(),
        ]);

        return response()->json([
            'data' => $registro 
        ]);
    }

    public function CloseAssigment(Request $request , string $id)
    {
        $data = $request->input('data');

        $task = TaskWeeklyPlan::find($id);
        $task->start_date = Carbon::now();
        $task->slots -= count($data);
        $task->save();


        foreach ($data as $item) {
            EmployeeTask::create([
                'task_weekly_plan_id' => $task->id,
                'employee_id' => $item['emp_id'],
                'code' => $item['code'],
                'name' => $item['name'],
            ]);
        }

        
        return response()->json([
            'message' => 'Assigment Closed'
        ]);
    }

    public function TaskDetail(string $id){
        $task = TaskWeeklyPlan::find($id);

       return new TaskWeeklyPlanDetailsResource($task);
    }

    public function CloseTask(string $id){
        $task = TaskWeeklyPlan::find($id);

        $task->end_date = Carbon::now();
        $task->save();
        return response()->json([
            'data' => $task
        ]);
    }

    public function destroy(string $id){
        $task = TaskWeeklyPlan::find($id);
        $task->delete();

        return response()->json([
            'message' => 'Task Deleted'
        ]);
    }

    public function update(EditTaskWeeklyPlanRequest $request, string $id){
        $data = $request->validated();
        $task = TaskWeeklyPlan::find($id);

        if($task->start_date){
            $draft_start_date = $data['start_date'] . ' ' . $data['start_time'];
            $start_date = Carbon::parse($draft_start_date);
        }

        if($task->end_date){
            $draft_end_date = $data['end_date'] . ' ' . $data['end_time'];
            $end_date = Carbon::parse($draft_end_date);
        }
       
      

        $task->budget = $data['budget'];
        $task->start_date = $start_date ?? null;
        $task->end_date = $end_date ?? null;
        $task->hours = $data['hours'];
        $task->weekly_plan_id = $data['weekly_plan_id'];
        $task->save();
  
        return response()->json([
            'message' => 'Task Updated Successfully',
            'data' => $task
        ]);
    }

    public function EraseAssignationTask(string $id){
        $task = TaskWeeklyPlan::find($id);
        $task->start_date = null;
        $task->end_date=null;
        $task->employees()->delete();
        $task->save();

        return response()->json([
            'message' => 'Task Cleaned'
        ]);
    }
}
