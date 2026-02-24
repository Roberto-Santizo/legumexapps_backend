<?php

namespace App\Repositories;

use App\Models\LoteChecklist;
use App\Models\LoteChecklistCondition;

class LoteRepository
{
    public function createLoteChecklist($data)
    {
        return LoteChecklist::create($data);
    }

    public function getLoteChecklistByLoteIdAndDate($loteId, $date)
    {
        return LoteChecklist::where('plantation_control_id', $loteId)->whereDate('created_at', $date)->first();
    }

    public function addLoteChecklistCondition($data)
    {
        return LoteChecklistCondition::create($data);
    }
}
