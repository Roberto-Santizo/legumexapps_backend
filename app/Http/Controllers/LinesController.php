<?php

namespace App\Http\Controllers;

use App\Http\Resources\LineDetailsByDayResource;
use App\Http\Resources\LineHoursPerWeekResource;
use App\Http\Resources\LinesResource;
use App\Http\Resources\LinesSelectResource;
use App\Imports\UpdatePositionsImport;
use App\Models\BiometricTransaction;
use App\Models\BitacoraLines;
use App\Models\Line;
use App\Models\WeeklyPlan;
use App\Models\WeeklyProductionPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $lines = Line::select('id', 'code', 'name')->paginate(10);
        } else {
            $lines = Line::select('id', 'code', 'name')->get();
        }

        return LinesResource::collection($lines);
    }

    public function GetAllLinesBySku(string $id)
    {
        $lines = Line::select('id', 'code', 'shift', 'name')
            ->whereHas('skus', function ($query) use ($id) {
                $query->where('sku_id', $id);
            })
            ->with(['skus' => function ($query) use ($id) {
                $query->where('sku_id', $id);
            }])
            ->get();

        return LinesSelectResource::collection($lines);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:lines,code',
            'shift' => 'required',
            'name' => 'required'
        ]);

        try {
            Line::create($data);

            return response()->json('Linea Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $line = Line::find($id);
        if (!$line) {
            return response()->json([
                'msg' => 'Line not Found'
            ], 404);
        }

        return new LinesResource($line);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            "code" => "required|unique:lines,code," . $id,
        ]);


        $line = Line::find($id);

        if (!$line) {
            return response()->json([
                'msg' => 'Line Not Found'
            ], 404);
        }

        try {
            if ($line->code != $data['code']) {
                BitacoraLines::create([
                    'line_id' => $line->id,
                    'old_code' => $line->code,
                    'new_code' => $data['code'],
                    'old_total_persons' => 0,
                    'new_total_persons' => 0
                ]);
            }

            $line->code = $data['code'];
            $line->save();

            return response()->json('Linea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UpdatePositions(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new UpdatePositionsImport($id), $request->file('file'));

            return response()->json('Asignaciones Actualizadas Correctamente', 200);
        } catch (\Throwable  $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetPerformanceByLine(Request $request, string $id)
    {
        $date = $request->query('date');

        $line = Line::find($id);

        if (!$line) {
            return response()->json([
                'msg' => 'Linea No Encontrada'
            ], 404);
        }

        try {
            $tasks = $line->tasks()->whereDate('operation_date', $date)->whereNot('end_date', null)->get();

            if ($tasks->isEmpty()) {
                return response()->json([
                    'max_value' => 0,
                    'summary' => [
                        'HBiometrico' => 0,
                        'HPlan' => 0,
                        'HLinea' => 0,
                        'HRendimiento' => 0,
                    ],
                    'details' => []
                ], 200);
            }

            $biometric_hours = 0;
            $line_hours = 0;
            $performance_hours = 0;
            $tasks_hours = 0;

            $tasks->map(function ($task) use (&$line_hours, &$performance_hours, &$tasks_hours) {
                $line_hours += $task->start_date->diffInHours($task->end_date);
                $tasks_hours += $task->total_hours;
                if ($task->line_sku->lbs_performance && $task->finished_tarimas) {
                    $total_boxes = $task->line_sku->sku->boxes_pallet * $task->finished_tarimas;
                    $lbs_teoricas = $task->line_sku->sku->presentation * $total_boxes;
                    $performance_hours = $lbs_teoricas / $task->line_sku->lbs_performance;
                } else {
                    $performance_hours = $task->start_date->diffInHours($task->end_date);
                }
            });


            $entrances = BiometricTransaction::where('pin', 'LIKE', '%' . $tasks->first()->line_sku->line->code . '%')
                ->whereDate('event_time', $tasks->first()->operation_date)
                ->get();


            $morning = $entrances->filter(function ($item) {
                return Carbon::parse($item->event_time)->format('H:i:s') < '12:00:00';
            });

            $afternoon = $entrances->filter(function ($item) {
                return Carbon::parse($item->event_time)->format('H:i:s') >= '12:00:00';
            });

            $last_in = Carbon::parse($morning->last()->create_time);
            $first_out =  Carbon::parse($afternoon->first()->create_time);

            $biometric_hours = $last_in->diffInHours($first_out);

            $summary = [
                'HBiometrico' => round($biometric_hours, 2),
                'HPlan' => $tasks_hours,
                'HLinea' => round($line_hours, 2),
                'HRendimiento' => round($performance_hours, 2),
            ];

            return response()->json([
                'max_value' => max($summary),
                'summary' => $summary,
                'details' => LineDetailsByDayResource::collection($tasks)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function GetHoursPerWeek(string $weekly_plan_id)
    {
        $weeklyplan = WeeklyProductionPlan::find($weekly_plan_id);

        if (!$weeklyplan) {
            return response()->json('Plan no encontrado', 404);
        }

        try {
            $tasks = $weeklyplan->tasks()->whereNotNull('operation_date')->get();

            $data = [];

            $data = $tasks->map(function ($task) {
                $performance = $task->line_sku->lbs_performance;
                $hours = $performance ? $task->total_lbs / $performance : 0;

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
                    'total_hours' => $items->sum('hours'),
                ];
            })->values();


            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 500);
        }
    }
}
