<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddImageCropDiseaseRequest;
use App\Http\Requests\CreateOrUpdateCropDiseaseRequest;
use App\Http\Resources\CropDiseaseImageCollection;
use App\Http\Resources\CropDiseaseResource;
use App\Http\Resources\CropDiseaseSymptomCollection;
use App\Services\CropDiseaseService;
use App\Services\UploadImageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $service = new CropDiseaseService();
            $response = $service->getCropDiseases($request);

            return response()->json([
                'statusCode' => 200,
                'response' => $response
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateCropDiseaseRequest $request)
    {
        try {
            $data = $request->validated();
            $service = new CropDiseaseService();

            $service->createCropDisease($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Enfermedad creada correctamente'
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
            $service = new CropDiseaseService();
            $cropDisease = $service->getCropDiseaseById($id);

            return response()->json([
                'statusCode' => 200,
                'response' => new CropDiseaseResource($cropDisease)
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
    public function update(CreateOrUpdateCropDiseaseRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $service = new CropDiseaseService();

            $service->updateCropDisease($id, $data);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Enfermedad actualizada correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    public function addImage(AddImageCropDiseaseRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $uploadImageService = new UploadImageService();
            $cropDiseaseService = new CropDiseaseService();

            $filename = $uploadImageService->uploadImage($data['image'], 'cropDisease');

            $cropDiseaseService->addImageCropDisease($id, $filename);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Imagen agregada correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    public function getCropDiseaseImages(Request $request, string $id)
    {
        try {
            $cropDiseaseService = new CropDiseaseService();
            $images = $cropDiseaseService->getCropDiseaseImages($id);

            return response()->json([
                'statusCode' => 200,
                'response' => new CropDiseaseImageCollection($images)
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    public function deleteCropDiseaseImage(Request $request, string $id)
    {
        try {
            $cropDiseaseService = new CropDiseaseService();
            $cropDiseaseService->deleteCropDiseaseImage($id);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Imagen eliminada correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    public function getCropDiseaseSymptoms(Request $request, string $id)
    {
        try {
            $cropDiseaseService = new CropDiseaseService();
            $symptoms = $cropDiseaseService->getCropDiseaseSymptoms($id);

            return response()->json([
                'statusCode' => 200,
                'response' => new CropDiseaseSymptomCollection($symptoms),
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }
}
