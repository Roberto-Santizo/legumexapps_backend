<?php

namespace App\Repositories;

use App\Models\CropDisease;

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
}
