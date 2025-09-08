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

    public function handle()
    {
        $changes = ProductionOperationChange::whereNull('notified_at')->get()->groupBy('user_id');

        foreach ($changes as $value) {
            if (count($value) > 0) {
                ProductionOperationChangeNotificationService::sendEmailNotification($changes);

                foreach ($value as $change) {
                    $change->notified_at = Carbon::now();
                    $change->save();
                }
                $this->info('Correo Enviado');
            } else {
                $this->info('No existen cambios pendientes');
                return;
            }
        }
    }
}
