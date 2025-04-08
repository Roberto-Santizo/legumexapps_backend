<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoteRequest;
use App\Http\Resources\FincaLotesResource;
use App\Http\Resources\LoteCollection;
use App\Imports\UpdateLotesImport;
use App\Models\Lote;
use App\Models\LotePlantationControl;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new LoteCollection(Lote::paginate(10));
    }
    public function GetAllLotes()
    {
        return new LoteCollection(Lote::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLoteRequest $request)
    {
        $data = $request->validated();

        try {
            $lote = Lote::create([
                'name' => $data['name'],
                'finca_id' => $data['finca_id']
            ]);

            LotePlantationControl::create([
                'lote_id' => $lote->id,
                'plantation_controls_id' => $data['cdp_id'],
                'status' => 1
            ]);

            return response()->json('Lote Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al crear el lote'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}


    public function GetLotesByFincaId(string $id)
    {
        $lotes = Lote::where('finca_id', $id)->get();
        return new LoteCollection($lotes);
    }

    public function UpdateLotes(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new UpdateLotesImport, $request->file('file'));

            return response()->json('Lotes Actualizados Correctamente', 200);
        } catch (Exception $th) {
            return response()->json([
                'errors' => 'Hubo un error al actualizar los lotes'
            ], 500);
        }
    }
}
