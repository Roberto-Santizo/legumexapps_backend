<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsumoRequest;
use App\Http\Resources\InsumoCollection;
use App\Imports\InsumosImport;
use App\Models\Insumo;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $insumo = Insumo::create([
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

    public function UploadInsumos(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        try {
            Excel::import(new InsumosImport, $request->file('file'));
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }

        return response()->json([
            'message' => 'Insumos Created Successfully'
        ]);
    }
}
