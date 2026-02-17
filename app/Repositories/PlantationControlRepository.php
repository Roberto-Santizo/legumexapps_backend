<?php

namespace App\Repositories;

use App\Models\PlantationControl;

class PlantationControlRepository
{
    public function getPlantationControlByLoteId($loteId)
    {
        return PlantationControl::where('lote_id', $loteId)->get()->last();
    }
}
