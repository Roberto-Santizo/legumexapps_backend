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
    public function index()
    {
        return new PlantationControlCollection(PlantationControl::paginate(10));
    }

    public function GetAllCDPS()
    {
        return new PlantationControlCollection(PlantationControl::all());
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
                'density' => $data['density'],
                'size' => $data['size'],
                'start_date' => $data['start_date'],
                'crop_id' => $data['crop_id'],
                'recipe_id' => $data['recipe_id']
            ]);

            return response()->json('CDP Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear el cdp'
            ], 500);
        }
    }

    public function GetCDPSByLoteId(string $id)
    {
        $lote = Lote::find($id);
        return response()->json([
            'data' => LotePlantationControlResource::collection($lote->lote_cdps)
        ]);
    }

    public function GetCDPInfo(Request $request)
    {
        $data = $request->validate([
            'lote_plantation_control_id' => 'required'
        ]);
        $lote_plantation_control = LotePlantationControl::find($data['lote_plantation_control_id']);
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
