<?php

namespace App\Repositories;

use App\Models\CropDiseaseSyptom;

class CropDiseaseSymptomRepository
{
    public function getCropDiseaseSymptoms()
    {
        return CropDiseaseSyptom::all();
    }

    public function createCropDiseaseSymptom($data)
    {
        return CropDiseaseSyptom::create($data);
    }

    public function getCropDiseaseSymptomById($id)
    {
        return CropDiseaseSyptom::find($id);
    }

    public function updateCropDiseaseSymptomById(CropDiseaseSyptom $entity, $data)
    {
        return $entity->update($data);
    }
}
