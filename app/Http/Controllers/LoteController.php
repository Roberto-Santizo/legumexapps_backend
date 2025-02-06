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
        
        $lote = Lote::create([
            'name' => $data['name'],
            'finca_id' => $data['finca_id']
        ]);

        $lote_cdp = LotePlantationControl::create([
            'lote_id' => $lote->id,
            'plantation_controls_id' => $data['cdp_id'],
            'status' => 1
        ]);

        return response()->json([
            'lote' => $lote,
            'lote_cdp' => $lote_cdp
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }


    public function GetLotesByFincaId(string $id){
        $lotes = Lote::where('finca_id',$id)->get();
        return new LoteCollection($lotes);
    }

    public function UpdateLotes(Request $request)
    {   
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new UpdateLotesImport, $request->file('file'));
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }

        return response()->json([
            'message' => 'Lotes Updated Successfully'
        ]);
    }
}
