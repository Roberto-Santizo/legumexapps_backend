<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSeedingPlanRequest;
use App\Http\Resources\SeedingPlansCollection;
use App\Http\Resources\SeedingPlanResource;
use App\Imports\SeedingPlanImport;
use App\Models\DraftWeeklyPlan;
use App\Models\TaskCropWeeklyPlan;
use App\Models\TaskInsumos;
use App\Models\TaskWeeklyPlan;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SeedingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = DraftWeeklyPlan::query();

            if ($request->query('finca')) {
                $query->whereHas('finca', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->query('finca') . '%');
                });
            }

            if ($request->query('week')) {
                $query->where('week', $request->query('week'));
            }
            if ($request->query('year')) {
                $query->where('year', $request->query('year'));
            }

            $limit = $request->query('limit');

            if ($limit) {
                return new SeedingPlansCollection($query->paginate($limit));
            } else {
                return new SeedingPlansCollection($query->get());
            }
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'msg' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSeedingPlanRequest $request)
    {
        $data = $request->validated();

        try {
            Excel::import(new SeedingPlanImport, $data['file']);

            return response()->json([
                "statusCode" => 201,
                "message" => "Plan de siembras creado correctamente"
            ], 201);
        } catch (HttpException  $th) {
            return response()->json([
                "statusCode" => $th->getStatusCode(),
                'msg' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $draft = DraftWeeklyPlan::find($id);
            $data = (new SeedingPlanResource($draft))->additional(['filter' => $request->query('cdp')]);

            return response()->json([
                "statusCode" => 200,
                "data" => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                "message" => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $draft_plan = DraftWeeklyPlan::find($id);

            if (!$draft_plan) {
                return response()->json([
                    "statusCode" => 404,
                    "message" => 'Plan no encontrado'
                ], 404);
            }

            $tasks = $draft_plan->tasks;

            $plan = WeeklyPlan::create([
                'finca_id' => $draft_plan->finca_id,
                'week' => $draft_plan->week,
                'year' => $draft_plan->year,
            ]);

            foreach ($tasks as $task) {
                $task_name = $task->taskGuide->task->name;
                if (str_contains($task_name, 'COSECHA')) {
                    TaskCropWeeklyPlan::create([
                        'weekly_plan_id' => $plan->id,
                        'plantation_control_id' => $task->plantation_control_id,
                        'tarea_id' => $task->taskGuide->task->id,
                    ]);
                } else {
                    $auxtask = TaskWeeklyPlan::create([
                        'weekly_plan_id' => $plan->id,
                        'tarea_id' => $task->taskGuide->task->id,
                        'plantation_control_id' => $task->plantation_control_id,
                        'workers_quantity' => $task->slots,
                        'budget' => $task->budget,
                        'hours' => $task->hours,
                        'slots' => $task->slots,
                        'extraordinary' => 0,
                    ]);

                    if ($task->taskGuide->insumos->count() > 0) {
                        foreach ($task->taskGuide->insumos as $insumo) {
                            TaskInsumos::create([
                                'insumo_id' => $insumo->insumo_id,
                                'task_weekly_plan_id' => $auxtask->id,
                                'assigned_quantity' => $insumo->quantity,
                            ]);
                        }
                    }
                }
            }

            $draft_plan->status = 1;
            $draft_plan->save();

            return response()->json([
                'statusCode' => 201,
                'message' => 'Plan confirmado correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => $th->getMessage(),
                "message" => 'Hubo un error'
            ], 500);
        }
    }
}
