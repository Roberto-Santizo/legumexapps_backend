<?php

namespace App\Repositories;

use App\Models\CropDisease;
use App\Models\CropDiseaseImage;

class CropDiseaseRepository
{
    public function createCropDisease($data)
    {
        return CropDisease::create($data);
    }

    public function getCropDiseases($query)
    {
        return $query->get();
    }

    public function getPaginatedCropDiseases($query)
    {
        return $query->paginate(10);
    }

    public function getCropDiseaseById($id)
    {
        return CropDisease::find($id);
    }

    public function updateCropDisease(CropDisease $cropDisease, $data)
    {
        return $cropDisease->update($data);
    }

    public function addImageToCropDisease($data)
    {
        return CropDiseaseImage::create($data);
    }

    public function getCropDiseaseImages(CropDisease $cropDisease)
    {
        return $cropDisease->images;
    }

    public function getCropDiseaseSymptoms(CropDisease $cropDisease)
    {
        return $cropDisease->symptoms;
    }
}
