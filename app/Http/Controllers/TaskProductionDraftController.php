<?php

namespace App\Http\Controllers;

use App\Events\UpdateProductionPlanification;
use App\Http\Requests\CreateDraftTaskPlan;
use App\Models\DraftWeeklyProductionPlan;
use App\Models\LineStockKeepingUnits;
use App\Models\TaskProductionDraft;
use Illuminate\Http\Request;

class TaskProductionDraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(String $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            $tasks = $draft->tasks()->with('line_performance')->with('sku')->get();

            $data = [];

            $data = $tasks->map(function ($task) {
                $performance = $task->line_performance->lbs_performance;
                $total_lbs = $task->sku->presentation * $task->total_boxes;
                $hours = $performance ? $total_lbs / $performance : 0;

                return [
                    'line_id' => strval($task->line_id),
                    'line' => $task->line->name,
                    'hours' => $hours
                ];
            });

            $grouped = $data->groupBy('line_id')->map(function ($items) {
                return [
                    'line_id' => $items->first()['line_id'],
                    'line' => $items->first()['line'],
                    'total_hours' => round($items->sum('hours'), 2),
                ];
            })->values();


            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDraftTaskPlan $request, string $id)
    {
        $data = $request->validated();


        $draft_plan = DraftWeeklyProductionPlan::find($id);

        if (!$draft_plan) {
            return response()->json([
                'msg' => 'Plan no Encontrado'
            ], 404);
        }

        try {
            TaskProductionDraft::create([
                'draft_weekly_production_plan_id' => $draft_plan->id,
                'line_id' => $data['line_id'],
                'stock_keeping_unit_id' => $data['stock_keeping_unit_id'],
                'total_lbs' => $data['total_lbs'],
                'destination' => $data['destination']
            ]);

            broadcast(new UpdateProductionPlanification());
            return response()->json('Tarea Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = TaskProductionDraft::select('id', 'line_id', 'stock_keeping_unit_id', 'total_lbs', 'destination')->where('id', $id)->first();

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            return response()->json($task);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateDraftTaskPlan $request, string $id)
    {
        $data = $request->validated();

        $task = TaskProductionDraft::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task->update($data);

            broadcast(new UpdateProductionPlanification());
            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $task = TaskProductionDraft::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task->delete();
            broadcast(new UpdateProductionPlanification());
            return response()->json('Tarea Eliminada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
