<?php

namespace App\Http\Controllers;

use App\Events\UpdateProductionPlanification;
use App\Http\Resources\DraftProductionPlanResource;
use App\Imports\TaskProductionDraftImport;
use App\Models\DraftWeeklyProductionPlan;
use App\Models\TaskProductionDraft;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WeeklyProductionPlanDraftController extends Controller
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
        $data = $request->validate([
            'week' => 'required'
        ]);

        try {
            $draft = DraftWeeklyProductionPlan::create([
                'week' => $data['week'],
                'year' => Carbon::now()->year,
            ]);

            return response()->json($draft->id);
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
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {

            $data = new DraftProductionPlanResource($draft);

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
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

    public function GetTasks(string $id)
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

    public function GetPackingMaterialNecessity(string $id)
    {

        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            $tasks = TaskProductionDraft::with('sku.items.item')->get();

            $resumen = [];

            foreach ($tasks as $task) {
                $totalLbs = $task->total_boxes * $task->sku->presentation;

                foreach ($task->sku->items as $recipeItem) {
                    $itemName = $recipeItem->item->name;
                    $itemCode = $recipeItem->item->code;
                    $requiredQty = $totalLbs * $recipeItem->lbs_per_item;

                    if (!isset($resumen[$itemCode])) {
                        $resumen[$itemCode] = 0;
                    }

                    $resumen[$itemCode] = [
                        'name' => $itemName,
                        'code' => $itemCode,
                        'quantity' => $requiredQty,
                    ];
                }
            }

            $resultado = [];

            foreach ($resumen as $key => $item) {
                $resultado[] = [
                    'code' => $key,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'inventory' => random_int(1, 100000)
                ];
            }
            return response()->json($resultado);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UploadTasks(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            Excel::import(new TaskProductionDraftImport($draft), $request->file('file'));

            broadcast(new UpdateProductionPlanification());
            return response()->json('Tareas Cargadas Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetRawMaterialNecessity(string $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            $tasks = TaskProductionDraft::with('sku.items.item')->get();

            $resumen = [];

            foreach ($tasks as $task) {
                $totalLbs = $task->total_boxes * $task->sku->presentation;

                foreach ($task->sku->products as $recipeItem) {
                    $itemName = $recipeItem->item->product_name;
                    $itemCode = $recipeItem->item->code;

                    $requiredQty = $totalLbs * $recipeItem->percentage;

                    if (!isset($resumen[$itemCode])) {
                        $resumen[$itemCode] = 0;
                    }

                    $resumen[$itemCode] = [
                        'name' => $itemName,
                        'code' => $itemCode,
                        'quantity' => $requiredQty,
                    ];
                }
            }

            $resultado = [];

            foreach ($resumen as $key => $item) {
                $resultado[] = [
                    'code' => $key,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'inventory' => random_int(1, 100000)
                ];
            }
            return response()->json($resultado);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
