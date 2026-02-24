<?php

namespace App\Messages;

use App\Models\LoteChecklistCondition;
use App\Models\PlantationControl;

class MessageBuilder
{
    public static function lotesValidationBuild(PlantationControl $cdp, LoteChecklistCondition $condition)
    {
        return view('emails.lotes-validation-email', compact('cdp', 'condition'))->render();
    }
}
