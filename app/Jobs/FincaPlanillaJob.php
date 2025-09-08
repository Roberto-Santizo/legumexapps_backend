<?php

namespace App\Jobs;

use App\Exports\FincaPlanillaExport;
use App\Models\User;
use App\Models\WeeklyPlan;
use App\Services\PlanillaFincaReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FincaPlanillaJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $weekly_plan_id;

    public function __construct($userId, $weekly_plan_id)
    {
        $this->userId = $userId;
        $this->weekly_plan_id = $weekly_plan_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);
        $weekly_plan = WeeklyPlan::find($this->weekly_plan_id);
        $file = Excel::raw(new FincaPlanillaExport($weekly_plan), \Maatwebsite\Excel\Excel::XLSX);

        try {
            PlanillaFincaReportService::sendEmailNotification($file, $weekly_plan, $user);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
