<?php

namespace App\Http\Controllers;

use App\Events\UpdateProductionPlanification;
use App\Http\Resources\DraftProductionPlanResourceDetails;
use App\Http\Resources\DraftWeeklyProductionPlanResource;
use App\Imports\TaskProductionDraftImport;
use App\Models\DraftWeeklyProductionPlan;
use App\Models\LineStockKeepingUnits;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionDraft;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyProductionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;

class WeeklyProductionPlanDraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $drafts = DraftWeeklyProductionPlan::orderBy('week', 'DESC')->paginate(10);
        } else {
            $drafts = DraftWeeklyProductionPlan::get();
        }

        return DraftWeeklyProductionPlanResource::collection($drafts);
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
            $year = Carbon::now()->year;

            $plan = WeeklyProductionPlan::where('year', $year)->where('week', $data['week'])->first();

            if ($plan) {
                return response()->json([
                    'msg' => 'El plan indicado ya existe'
                ], 405);
            }

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

            $data = new DraftProductionPlanResourceDetails($draft);

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
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
            $tasks = TaskProductionDraft::with('sku.items.item')->where('draft_weekly_production_plan_id', $draft->id)->get();

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
                    'inventory' => 0
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
            $tasks = TaskProductionDraft::with('sku.items.item')->where('draft_weekly_production_plan_id', $draft->id)->get();

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
                    'inventory' => 0
                ];
            }
            return response()->json($resultado);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function ConfirmPlan(Request $request, string $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            $payload = JWTAuth::getPayload();
            $role = $payload->get('role');

            if ($role === 'admin') {
                $draft->production_confirmation = true;
                $draft->bodega_confirmation = true;
                $draft->logistics_confirmation = true;
                $draft->confirmation_date = Carbon::now();
                $draft->save();
            }

            broadcast(new UpdateProductionPlanification());
            return response()->json('Confirmado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function CreateWeeklyProductionPlan(Request $request, string $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            if (!$draft->production_confirmation || !$draft->bodega_confirmation || !$draft->logistics_confirmation) {
                return response()->json([
                    'msg' => 'El plan no se puede confirmar por falta de autorizaciones'
                ], 405);
            }

            $plan_exists = WeeklyProductionPlan::where('year', $draft->year)->where('week', $draft->week)->first();

            if ($plan_exists) {
                return response()->json([
                    'msg' => 'El plan ya existe'
                ], 405);
            }

            $tasks = $draft->tasks;
            $performances = LineStockKeepingUnits::all();
            $skus = StockKeepingUnit::all();

            $weekly_plan = WeeklyProductionPlan::create([
                'week' => $draft->week,
                'year' => $draft->year
            ]);

            foreach ($tasks as $task) {
                $performance = $performances->where('line_id', $task->line_id)->where('sku_id', $task->stock_keeping_unit_id)->first();
                $sku = $skus->where('id', $task->stock_keeping_unit_id)->first();
                $total_lbs = $task->total_boxes * $sku->presentation;
                $total_hours = $total_lbs / $performance->lbs_performance;

                TaskProductionPlan::create([
                    'line_id' => $task->line_id,
                    'weekly_production_plan_id' => $weekly_plan->id,
                    'operation_date' => null,
                    'total_hours' => round($total_hours, 2),
                    'line_sku_id' => $performance->id,
                    'status' =>  1,
                    'destination' => $task->destination,
                    'total_lbs' => $total_lbs
                ]);
            }

            broadcast(new UpdateProductionPlanification());
            return response()->json('Plan Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
