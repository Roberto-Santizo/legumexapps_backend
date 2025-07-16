<?php

namespace App\Console\Commands;

use App\Models\ProductionOperationChange;
use App\Services\ProductionOperationChangeNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendProductionChangesEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-production-changes-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia email de cambios de producción';
    /**
     * Execute the console command.
     */

    protected ProductionOperationChangeNotificationService $notificationService;

    public function __construct(ProductionOperationChangeNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $today = Carbon::now();
        $changes = ProductionOperationChange::whereDate('created_at', $today)->whereNull('notified_at')->get()->groupBy('user_id');

        foreach ($changes as $value) {
            if (count($value) > 0) {
                $this->notificationService->sendNotification($value);

                foreach ($value as $change) {
                    $change->notified_at = Carbon::now();
                    $change->save();
                }
            } else {
                return;
            }
        }
         $this->info('Correo Enviado');
    }
}
