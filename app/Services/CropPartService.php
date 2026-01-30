<?php

namespace App\Services;

use App\Models\CropPart;
use App\Repositories\CropPartRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CropPartService
{
    private $service;

    public function __construct()
    {
        $this->service = new CropPartRepository();
    }

    public function getCropParts($request)
    {
        $query = CropPart::query();

        if ($request->query('crop')) {
            $query->where('crop_id', $request->query('crop'));
        }

        return $request->query('page') ? $this->service->getPaginatedCropParts($query) : $this->service->getCropParts($query);
    }

    public function getCropPartById(string $id)
    {
        $cropPart = $this->service->getCropPartById($id);

        if (!$cropPart) {
            throw new HttpException(404, 'La parte no existe');
        }

        return $cropPart;
    }

    public function createCropPart($data)
    {
        return $this->service->createCropPart($data);
    }

    public function updateCropPart($data, $id)
    {
        $cropPart = $this->getCropPartById($id);

        return $this->service->updateCropPart($cropPart, $data);
    }
}
