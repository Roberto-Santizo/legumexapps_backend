<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCDPRequest;
use App\Http\Requests\UpdateCdpDatesRequest;
use App\Http\Resources\PlantationControlCollection;
use App\Models\AnnualSalary;
use App\Models\DraftWeeklyPlan;
use App\Models\PlantationControl;
use App\Models\TaskGuideline;
use App\Models\TaskWeeklyPlanDraft;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CDPController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlantationControl::query();

        if ($request->query('cdp')) {
            $query->where('name', $request->query('cdp'));
        }

        if ($request->query('end_date')) {
            $query->whereDate('end_date', $request->query('end_date'));
        }

        if ($request->query('start_date')) {
            $query->whereDate('start_date', $request->query('start_date'));
        }

        if ($request->query('paginated')) {
            return new PlantationControlCollection($query->paginate(10));
        } else {
            return new PlantationControlCollection($query->get());
        }
    }

    public function show(string $id)
    {
        $cdp = PlantationControl::find($id);

        if (!$cdp) {
            return response()->json([
                'msg' => 'No se encontró el CDP'
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'response' => [
                'id' => "{$cdp->id}",
                'name' => $cdp->name,
                "lote" => $cdp->lote->name,
                'start_date' => $cdp->start_date->format('Y-m-d'),
                'end_date' => $cdp->end_date->format('Y-m-d')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCDPRequest $request)
    {
        $data = $request->validated();

        try {
            PlantationControl::create([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);

            return response()->json('CDP Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCdpDatesRequest $request, string $id)
    {
        $data = $request->validated();
        $cdp = PlantationControl::find($id);

        if (!$cdp) {
            return response()->json([
                'msg' => 'No se encontró el CDP'
            ], 404);
        }

        try {
            $newStartDate = Carbon::parse($data['start_date']);
            $newEndDate = Carbon::parse($data['end_date']);

            if ($newStartDate->year != $newEndDate->year) {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'El año de las fechas no pueden ser diferentes'
                ], 400);
            }

            if ($newEndDate->isBefore($newStartDate)) {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'La fecha final no puede ser antes que la fecha de inicio'
                ], 400);
            }

            if ($newStartDate->diffInWeeks($newEndDate) < 1) {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'Las fechas no coinciden'
                ], 400);
            }

            $flag = ($newStartDate != $cdp->start_date) || ($newEndDate != $cdp->end_date) ? true : false;

            if ($flag) {
                $cdp->draftTasks()->delete();
                $year = $newStartDate->year;
                $weeks = range($newStartDate->weekOfYear, $newEndDate->weekOfYear);

                foreach ($weeks as $index => $week) {
                    $draft_weekly_plan = DraftWeeklyPlan::where('week', $week)->where('year', $year)->first();
                    if (!$draft_weekly_plan) {
                        $draft_weekly_plan = DraftWeeklyPlan::create([
                            'year' => $year,
                            'week' => $week,
                            'finca_id' => $cdp->lote->finca_id
                        ]);
                    }

                    $tasks = $this->getTasks($cdp->recipe_id, $cdp->crop_id, $cdp->lote->finca_id, $index + 1);

                    foreach ($tasks as $task) {

                        $slots = $task->hours / 8;
                        $hour = AnnualSalary::all();
                        $hours = $cdp->lote->size * $task->hours_per_size;

                        TaskWeeklyPlanDraft::create([
                            'task_guideline_id' => $task->id,
                            'hours' => $hours,
                            'budget' => $hours * $hour->last()->amount,
                            'slots' => $slots < 0 ? 1 : floor($slots),
                            'draft_weekly_plan_id' => $draft_weekly_plan->id,
                            'plantation_control_id' => $cdp->id
                        ]);
                    }
                }
            }

            $cdp->start_date = $newStartDate;
            $cdp->end_date = $newEndDate;

            $cdp->save();

            return response()->json([
                'statusCode' => 200,
                'message' => 'CDP Actualizado Correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    private function getTasks(string $recipe_id, string $crop_id, string $finca_id, int $week)
    {
        $tasks = TaskGuideline::where('recipe_id', $recipe_id)->where('crop_id', $crop_id)->where('finca_id', $finca_id)->where('week', $week)->get();

        return $tasks;
    }
}
