<?php

namespace App\Http\Controllers;

use App\Http\Resources\LineDetailsByDayResource;
use App\Http\Resources\LinesResource;
use App\Http\Resources\LinesSelectResource;
use App\Imports\UpdatePositionsImport;
use App\Models\BiometricEmployee;
use App\Models\BiometricTransaction;
use App\Models\BitacoraLines;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

use function PHPUnit\Framework\isEmpty;

class LinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lines = Line::select('id', 'code', 'name')->paginate(10);

        return LinesResource::collection($lines);
    }

    public function GetAllLines()
    {
        $lines = Line::select('id', 'code', 'shift', 'name')->get();

        return LinesSelectResource::collection($lines);
    }

    public function GetAllLinesBySku(string $id)
    {
        $lines = Line::select('id', 'code', 'total_persons', 'shift', 'name')
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
}
