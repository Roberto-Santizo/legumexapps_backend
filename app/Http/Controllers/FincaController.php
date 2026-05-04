<?php

namespace App\Http\Controllers;

use App\Http\Resources\FincaCollection;
use App\Http\Resources\LoteCollection;
use App\Models\Finca;
use App\Models\Lote;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new FincaCollection(Finca::all());
    }

    public function show(string $id)
    {
        $lotes = Lote::where('finca_id', $id)->get();
        return new LoteCollection($lotes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required'
        ]);

        try {
            Finca::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'terminal_id' => 0
            ]);

            return response()->json('Finca Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
