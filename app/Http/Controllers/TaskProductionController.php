<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskProductionPlanDetailsResource;
use App\Http\Resources\TaskProductionPlanResource;
use App\Models\TaskProductionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks_production_plan = TaskProductionPlan::all();
        return TaskProductionPlanResource::collection($tasks_production_plan);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        return new TaskProductionPlanDetailsResource($task_production_plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'line_id' => 'required',
            'operation_date' => 'required',
            'total_hours' => 'required',
        ]);

        $task_production_plan = TaskProductionPlan::find($id);

        if(!$task_production_plan){
            return response()->json([
                'msg' => 'Task Production Plan Not Found' 
            ],404);
        }
        
        try {
            $task_production_plan->update($data);

            return response()->json([
                'msg' => 'Task Production Plan Updated Successfully' 
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage() 
            ],500);
        }
    }
}
