<?php

namespace App\Services;

use App\Abstracts\EmailProvider;
use App\Models\LoteChecklistCondition;
use App\Models\PlantationControl;

class EmailService
{
    private $service;

    public function __construct(EmailProvider $emailprovider)
    {
        $this->service = $emailprovider;
    }

    public function sendLoteValidationEmail(PlantationControl $cdp, LoteChecklistCondition $condition)
    {
        $this->service->sendLotesValidationEmail($cdp, $condition);
    }
}
