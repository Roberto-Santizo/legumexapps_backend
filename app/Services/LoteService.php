<?php

namespace App\Services;

use App\Providers\EmailProvider;
use App\Repositories\LoteRepository;
use Carbon\Carbon;

class LoteService
{
    private $service;
    private $cdpService;

    public function __construct()
    {
        $this->service = new LoteRepository();
        $this->cdpService = new PlantationControlService();
    }

    public function getLoteChecklistByLoteIdAndDate($cdpId, $date, $userId)
    {
        $checklist = $this->service->getLoteChecklistByLoteIdAndDate($cdpId, $date);

        if (!$checklist) {
            $checklist = $this->service->createLoteChecklist(['user_id' => $userId, 'plantation_control_id' => $cdpId, 'created_at' => $date]);
        }

        return $checklist;
    }

    public function createLoteChecklist($userId, $loteId, $data)
    {
        $cdp = $this->cdpService->getPlantationControlByLoteId($loteId);
        $checklist = $this->getLoteChecklistByLoteIdAndDate($cdp->id, Carbon::now(), $userId);
        $emailProvider = new EmailProvider();
        $emailService = new EmailService($emailProvider);

        foreach ($data as $condition) {
            $condition['lote_checklist_id'] = $checklist->id;
            $condition = $this->service->addLoteChecklistCondition($condition);

            if ($condition['exists']) {
                $emailService->sendLoteValidationEmail($cdp, $condition);
            }
        }
    }
}
