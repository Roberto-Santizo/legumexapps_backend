<?php

namespace App\Services;

use App\Models\CropDisease;
use App\Repositories\CropDiseaseRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseService
{
    private $service;

    public function __construct()
    {
        $this->service = new CropDiseaseRepository();
    }

    public function createCropDisease($data)
    {
        return $this->service->createCropDisease($data);
    }

    public function getCropDiseases($request)
    {
        $query = CropDisease::query();
        $paginatedFlag = $request->query('page');

        if ($request->query('crop')) {
            $query->where('crop_id', $request->query('crop'));
        }

        return $paginatedFlag ? $this->service->getPaginatedCropDiseases($query) : $this->service->getCropDiseases($query);
    }

    public function getCropDiseaseById(string $id)
    {
        $cropDisease = $this->service->getCropDiseaseById($id);

        if (!$cropDisease) {
            throw new HttpException(404, 'Enfermedad no encontrada');
        }

        return $cropDisease;
    }

    public function updateCropDisease(string $id, $data)
    {
        $cropDisease = $this->getCropDiseaseById($id);

        return $this->service->updateCropDisease($cropDisease, $data);
    }
}
