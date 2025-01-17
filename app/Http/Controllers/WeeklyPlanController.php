<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use App\Imports\WeeklyPlanImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\WeeklyPlanCollection;
use App\Models\LotePlantationControl;

class WeeklyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(string $id)
    {
        $plan = WeeklyPlan::find($id);
        if (!$plan) {
            return response()->json([
                'errors' => ['El plan no existe']
            ],404);
        }
        $tasks_by_lote = $plan->tasks->groupBy('lote_plantation_control_id');


        $data = $tasks_by_lote->map(function ($group, $key) {
            return [
                'lote' => LotePlantationControl::find($key)->lote->name,
                'lote_plantation_control_id' => strval($key),
                'total_budget' => $group->sum('budget'),
                'total_workers' => $group->sum('workers_quantity'),
                'total_hours' => $group->sum('hours'),
                'total_tasks' => $group->count(),
                'finished_tasks' => $group->filter(function ($task) {
                    return !is_null($task->end_date);
                })->count(),
            ];
        })->values();



        return response()->json([
            'data' => [
                'finca' => $plan->finca->name,
                'week' => $plan->week,
                'year' => $plan->year,
                'summary' => $data,
            ]
        ]);
    }

}
