<?php

namespace App\Repositories;

use App\Models\CropPart;

class CropPartRepository
{
    public function getCropParts($query)
    {
        return $query->get();
    }

    public function getPaginatedCropParts($query)
    {
        return $query->paginate(10);
    }

    public function getCropPartById(string $id)
    {
        return CropPart::find($id);
    }

    public function createCropPart($data)
    {
        return CropPart::create($data);
    }

    public function updateCropPart(CropPart $cropPart, $data)
    {
        return $cropPart->update($data);
    }
}
