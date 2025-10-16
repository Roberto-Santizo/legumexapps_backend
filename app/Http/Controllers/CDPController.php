<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCDPRequest;
use App\Http\Resources\LotePlantationControlResource;
use App\Http\Resources\PlantationControlCollection;
use App\Http\Resources\TaskCDPDetailResource;
use App\Imports\CDPSImport;
use App\Models\Lote;
use App\Models\LotePlantationControl;
use App\Models\PlantationControl;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $lote_plantation_control = LotePlantationControl::find($id);

        if (!$lote_plantation_control) {
            return response()->json([
                'msg' => 'No se encontró el CDP'
            ], 404);
        }

        $data_lote = [
            'lote' => $lote_plantation_control->lote->name,
            'cdp' => $lote_plantation_control->cdp->name,
            'start_date_cdp' => $lote_plantation_control->cdp->start_date,
            'end_date_cdp' => $lote_plantation_control->cdp->end_date ?? null,
        ];

        $data = TaskCDPDetailResource::collection($lote_plantation_control->tasks)->groupBy(fn($task) => $task->plan->week);

        return response()->json([
            'data_lote' => $data_lote,
            'data' => $data
        ]);
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

    public function UploadCDPS(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new CDPSImport, $request->file('file'));
            return response()->json('CDPS Actualizados Correctamente', 200);
        } catch (Exception $th) {
            return response()->json([
                'errors' => 'Hubo un error al actualizar la tarea'
            ], 500);
        }
    }
}
