<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrUpdateCropDiseaseSymptomRequest;
use App\Services\CropDiseaseSymptomService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseSymptomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $service = new CropDiseaseSymptomService();
            $data = $service->getCropDiseaseSymptoms($request);

            return response()->json([
                'statusCode' => 200,
                'response' => $data
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateCropDiseaseSymptomRequest $request)
    {
        try {
            $data = $request->validated();
            $service = new CropDiseaseSymptomService();
            $service->createCropDiseaseSymptom($data);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Sintoma creado correctamente'
            ], 200);
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
            $service = new CropDiseaseSymptomService();
            $cropDiseaseSymptom = $service->getCropDiseaseSymptomById($id);

            return response()->json([
                'statusCode' => 200,
                'response' => $cropDiseaseSymptom
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateCropDiseaseSymptomRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $service = new CropDiseaseSymptomService();
            $service->updateCropDiseaseSymptomById($id, $data);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Simtoma actualizado correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }
}
