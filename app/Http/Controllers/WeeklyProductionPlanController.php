<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeeklyPlanProductionResource;
use App\Imports\WeeklyProductionPlanImport;
use App\Models\Line;
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
    
        $groupedTasks = $weekly_plan->tasks->groupBy(function ($task) {
            return $task->line->code;
        });
    
        $lineas = $groupedTasks->keys()->map(function($linea){
            $line = Line::where('code',$linea)->first();
            return [
                'id' => strval($line->id),
                'line' => $linea
            ];
        });  
        return response()->json([
            'data' => $lineas
        ]);
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
            ], 200);
        } catch (\Throwable  $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
