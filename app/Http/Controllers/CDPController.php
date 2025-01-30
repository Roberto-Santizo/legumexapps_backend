<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCDPRequest;
use App\Http\Resources\PlantationControlCollection;
use App\Models\PlantationControl;
use Illuminate\Http\Request;

class CDPController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PlantationControlCollection(PlantationControl::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCDPRequest $request)
    {
        $data = $request->validated();

        $cdp = PlantationControl::create([
            'name' => $data['name'],
            'density' => $data['density'],
            'size' => $data['size'],
            'start_date' => $data['start_date'],
            'crop_id' => $data['crop_id'],
            'recipe_id' => $data['recipe_id']
        ]);

        return response()->json([
            'data' => $cdp,
            'message' => 'CDP creado exitosamente'
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
