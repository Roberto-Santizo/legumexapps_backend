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
    public function index(Request $request)
    {
        $query = Insumo::query();

        if ($request->query('code')) {
            $query->where('code', $request->query('code'));
        };

        if ($request->query('name')) {
            $query->where('name', 'LIKE', '%' . $request->query('name') . '%');
        };

        return new InsumoCollection($query->paginate(10));
    }

    public function getAllInsumos()
    {
        return new InsumoCollection(Insumo::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInsumoRequest $request)
    {
        $data = $request->validated();

        try {
            Insumo::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'measure' => $data['measure']
            ]);

            return response()->json('Insumo Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear el insumo'
            ], 500);
        }
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
            return response()->json('Insumos Creados Correctamente', 200);
        } catch (Exception $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear los insumos'
            ], 500);
        }

        return response()->json([
            'message' => 'Insumos Created Successfully'
        ]);
    }
}
