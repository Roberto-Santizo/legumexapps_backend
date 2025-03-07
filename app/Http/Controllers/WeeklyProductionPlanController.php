<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskProductionPlanResource;
use App\Http\Resources\WeeklyPlanProductionResource;
use App\Imports\WeeklyProductionPlanImport;
use App\Models\WeeklyProductionPlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WeeklyProductionPlanController extends Controller
{
    public function index()
    {
        $plans_production = WeeklyProductionPlan::paginate(10);
        return WeeklyPlanProductionResource::collection($plans_production);
    }

    public function show(string $id)
    {
        $weekly_plan = WeeklyProductionPlan::find($id);

        return TaskProductionPlanResource::collection($weekly_plan->tasks);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new WeeklyProductionPlanImport, $request->file('file'));

            return response()->json([
                'msg' => 'Plan Creado Correctamente'
            ],200);
        } catch (\Throwable  $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
