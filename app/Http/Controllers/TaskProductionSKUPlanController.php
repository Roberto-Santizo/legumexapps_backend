<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskProductionSKUPlanResource;
use App\Models\TaskProductionPlan;
use App\Models\TaskProductionStockKeepingUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskProductionSKUPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks_production = TaskProductionStockKeepingUnit::all();
        return TaskProductionSKUPlanResource::collection($tasks_production); 
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
        $tasks_production = TaskProductionStockKeepingUnit::where('task_p_id',$id)->get();
        return TaskProductionSKUPlanResource::collection($tasks_production);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'task_production_plan_id' => 'required'
        ]);

        $task_production_sku = TaskProductionStockKeepingUnit::find($id);
        $task_production = TaskProductionPlan::find($data['task_production_plan_id']);
        if (!$task_production_sku) {
            return response()->json([
                'msg' => 'Task Production SKU Not Found'
            ], 404);
        }

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Not Found'
            ], 404);
        }

        try {
            $task_production_sku->task_p_id = $task_production->id;
            $task_production_sku->save();

            return response()->json([
                'msg' => 'Task Production SKU Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

   
}
