<?php

namespace App\Http\Controllers;

use App\Models\TaskProductionPlan;
use App\Models\TaskProductionPlanNote;
use Illuminate\Http\Request;

class TaskProductionPlanNotesController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'reason' => 'required',
            'action' => 'required',
            'task_p_id' => 'required|exists:task_production_plans,id'
        ]);

        $task_production = TaskProductionPlan::find($data['task_p_id']);

        try {
            TaskProductionPlanNote::create($data);
            $task_production->is_justified = true;
            $task_production->save();
            return response()->json('Nota tomada correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
