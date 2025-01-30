<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoteRequest;
use App\Http\Resources\LoteCollection;
use App\Models\Lote;
use App\Models\LotePlantationControl;
use App\Models\PlantationControl;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new LoteCollection(Lote::paginate(10));
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
        //
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
}
