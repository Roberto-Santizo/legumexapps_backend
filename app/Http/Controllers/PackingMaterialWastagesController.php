<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialWastageRequest;
use App\Models\PackingMaterialWastage;
use Illuminate\Http\Request;

class PackingMaterialWastagesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackingMaterialWastageRequest $request)
    {
        $data = $request->validated();

        try {
            PackingMaterialWastage::create($data);

            return response()->json('Registro Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 200);
        }
    }
}
