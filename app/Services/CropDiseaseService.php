<?php

namespace App\Services;

use App\Models\CropDisease;
use App\Repositories\AgricolaImageRepository;
use App\Repositories\CropDiseaseRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseService
{
    private $service, $imageService;

    public function __construct()
    {
        $this->service = new CropDiseaseRepository();
        $this->imageService = new AgricolaImageRepository();
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

    public function addImageCropDisease(string $diseaseId, string $path)
    {
        $cropDisease = $this->getCropDiseaseById($diseaseId);
        $image = $this->imageService->createAgricolaImage(['image' => $path, 'slug' => $path]);

        return $this->service->addImageToCropDisease(['agricola_image_id' => $image->id, 'crop_disease_id' => $cropDisease->id]);
    }

    public function getCropDiseaseImages(string $diseaseId)
    {
        $cropDisease = $this->getCropDiseaseById($diseaseId);
        
        return $this->service->getCropDiseaseImages($cropDisease);
    }

    public function getCropDiseaseSymptoms(string $diseaseId)
    {
        $cropDisease = $this->getCropDiseaseById($diseaseId);
        
        return $this->service->getCropDiseaseSymptoms($cropDisease);
    }
}
