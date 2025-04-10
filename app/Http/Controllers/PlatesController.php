<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlatesResource;
use App\Models\Plate;
use Illuminate\Http\Request;

class PlatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plates = Plate::paginate(10);

        return PlatesResource::collection($plates);
    }

    public function getAllPlatesByCarrierId(string $id)
    {
        $plates = Plate::where('carrier_id',$id)->get();
        return PlatesResource::collection($plates);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:plates,name',
            'carrier_id' => 'required'
        ]);

        try {
            Plate::create($data);

            return response()->json('Placa Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
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
