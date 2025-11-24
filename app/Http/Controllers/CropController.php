<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCropRequest;
use App\Http\Resources\CropResource;
use App\Models\Crop;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $crops = CropResource::collection(Crop::all());

        return response()->json([
            "statusCode" => 200,
            "data" => $crops
        ], 200);
    }

    public function store(CreateCropRequest $request)
    {
        $data = $request->validated();
        try {
            Crop::create($data);

            return response()->json([
                "statusCode" => 201,
                "message" => "Cultivo Creado Correctamente"
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                "message" => "Hubo un error"
            ], 500);
        }
    }
}
