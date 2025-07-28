<?php

namespace App\Console\Commands;

use App\Models\LineStockKeepingUnits;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveDuplicatedSkusCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicated-skus-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Duplicated Skus Codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $groupedSkus = StockKeepingUnit::all()->groupBy('code');

        DB::transaction(function () use ($groupedSkus) {
            foreach ($groupedSkus as $key => $skus) {
                if ($skus->count() <= 1) continue;
                $first = $skus[0];
                unset($skus[0]);

                foreach ($skus as $sku) {
                    $tasks = TaskProductionPlan::whereHas('line_sku', function ($q) use ($sku) {
                        $q->where('sku_id', $sku->id);
                    })->get();

                    foreach ($tasks as $task) {

                        $line_sku = LineStockKeepingUnits::where('sku_id', $first->id)
                            ->where('line', $task->line_id)
                            ->first();

                        if ($line_sku) {
                            $task->line_sku_id = $line_sku->id;

                            $task->save();
                        } else {
                            Log::warning("No se encontró line_sku para SKU {$first->id} y línea {$task->line_id}");
                        }
                    }

                    LineStockKeepingUnits::where('sku_id', $sku->id)->delete();
                    $sku->items()->delete();

                    $sku->delete();
                    Log::info("SKU duplicado {$sku->id} eliminado.");
                }
            }
        });
    }
}
