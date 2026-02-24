<?php

namespace App\Services;

use App\Models\CropDiseaseSyptom;
use App\Models\PlantationControl;
use App\Repositories\CropDiseaseSymptomRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropDiseaseSymptomService
{
    private $service;

    public function __construct()
    {
        $this->service = new CropDiseaseSymptomRepository();
    }

    public function getCropDiseaseSymptoms($req)
    {
        $query = CropDiseaseSyptom::query();

        if ($req->query('cropDisease')) {
            $query->where('crop_disease_id', $req->query('cropDisease'));
        }

        if ($req->query('lote')) {
            $cdp = PlantationControl::where('lote_id', $req->query('lote'))->get()->last();
            $query->whereHas('disease', function ($q) use ($cdp) {
                $q->where('crop_id', $cdp->crop_id);
            });
        }

        return $this->service->getCropDiseaseSymptoms($query);
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
