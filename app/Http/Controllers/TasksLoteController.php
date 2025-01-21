<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskLoteResource;
use App\Http\Resources\TaskWeeklyPlanResource;
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
        ]);

        $tasks = TaskWeeklyPlan::where('lote_plantation_control_id', $data['id'])->get();

        return [
            'week' => $tasks->first()->plan->week,
            'finca' => $tasks->first()->plan->finca->name,
            'lote' => $tasks->first()->lotePlantationControl->lote->name,
            'data' => TaskWeeklyPlanResource::collection($tasks),
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
        $data = TaskWeeklyPlan::where('lote_plantation_control_id', $id)->get();

        return [
            'week' => $data->first()->plan->week,
            'finca' => $data->first()->plan->finca->name,
            'data' => TaskWeeklyPlanResource::collection($data),
        ];
  
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
}
