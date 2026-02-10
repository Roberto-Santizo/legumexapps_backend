<?php

namespace App\Services;

use App\Repositories\PlantationControlRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlantationControlService
{
    private $service;

    public function __construct()
    {
        $this->service = new PlantationControlRepository();
    }

    public function getPlantationControlByLoteId($loteId)
    {
        $cdp = $this->service->getPlantationControlByLoteId($loteId);

        if (!$cdp) {
            throw new HttpException(404, 'La parte no existe');
        }

        return $cdp;
    }
}
