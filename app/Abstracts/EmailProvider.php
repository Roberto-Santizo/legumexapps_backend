<?php

namespace App\Abstracts;

use App\Models\LoteChecklistCondition;
use App\Models\PlantationControl;

abstract class EmailProvider
{
    abstract public static function getAccessToken();
    abstract public function sendLotesValidationEmail(PlantationControl $cdp, LoteChecklistCondition $condition);
}
