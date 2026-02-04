<?php

namespace App\Services;

use App\Repositories\CropDiseaseSymptomRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseSymptomService
{
    private $service;

    public function __construct()
    {
        $this->service = new CropDiseaseSymptomRepository();
    }

    public function getCropDiseaseSymptoms()
    {

        return $this->service->getCropDiseaseSymptoms();
    }

    public function createCropDiseaseSymptom($data)
    {

        return $this->service->createCropDiseaseSymptom($data);
    }

    public function getCropDiseaseSymptomById($id)
    {
        $cropDiseaseSymptom = $this->service->getCropDiseaseSymptomById($id);

        if (!$cropDiseaseSymptom) {
            throw new HttpException(404, "Sintoma no encontrado");
        }

        return $cropDiseaseSymptom;
    }

    public function updateCropDiseaseSymptomById($id, $data)
    {
        $cropDiseaseSymptom = $this->getCropDiseaseSymptomById($id);

        return $this->service->updateCropDiseaseSymptomById($cropDiseaseSymptom, $data);
    }
}
