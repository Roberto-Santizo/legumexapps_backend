<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsumoRequest;
use App\Http\Resources\InsumoCollection;
use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new InsumoCollection(Insumo::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInsumoRequest $request)
    {
        $data = $request->validated();

        $insumo = Insumo::created([
            'name' => $data['name'],
            'code' => $data['code'],
            'measure' => $data['measure']
        ]);

        return response()->json([
            'data' => $insumo
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
