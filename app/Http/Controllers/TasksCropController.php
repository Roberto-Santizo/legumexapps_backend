<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCropResource;
use App\Models\TaskCropWeeklyPlan;
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

        $tasks = TaskCropWeeklyPlan::where('lote_plantation_control_id', $data['lote_plantation_control_id'])->where('weekly_plan_id',$data['weekly_plan_id'])->get();
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
        //
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
}
