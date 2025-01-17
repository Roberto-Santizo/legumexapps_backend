<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskLoteResource;
use App\Http\Resources\TaskWeeklyPlanResource;
use App\Models\TaskWeeklyPlan;
use Illuminate\Http\Request;

class TasksLoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        return TaskWeeklyPlanResource::collection($data);
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
