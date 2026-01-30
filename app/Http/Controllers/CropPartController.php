<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCropPartRequest;
use App\Services\CropPartService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropPartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $service = new CropPartService();
            $response = $service->getCropParts($request);

            return response()->json([
                'statusCode' => 200,
                'response' => $response
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ],  $th->getStatusCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCropPartRequest $request)
    {
        $data = $request->validated();

        try {
            $service = new CropPartService();
            $service->createCropPart($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Parte Creada Correctamente'
            ], 201);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $service = new CropPartService();

            return response()->json([
                'statusCode' => 200,
                'response' => $service->getCropPartById($id)
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ],  $th->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCropPartRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $service = new CropPartService();

            $service->updateCropPart($data, $id);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Parte Actualizada Correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ],  $th->getStatusCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
