<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionPlan;
use App\Models\TaskProductionStockKeepingUnit;
use App\Models\WeeklyProductionPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class WeeklyProductionPlanImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $date = Date::excelToDateTimeObject($row['fecha_de_operacion']);
            $year = Carbon::now()->year;
            $week = Carbon::now()->weekOfYear + 1;
            $weekly_production_plan = $this->createWeeklyProductionPlan($week, $year);
            $line = Line::where('code', $row['linea'])->first();
            $sku = StockKeepingUnit::where('code', $row['sku'])->first();

            if (!$line) {
                throw new Exception("Line Not Found");
            }

            if (!$sku) {
                throw new Exception("SKU Not Found");
            }

            try {
                $task_production_plan = TaskProductionPlan::create([
                    'line_id' => $line->id,
                    'weekly_production_plan_id' => $weekly_production_plan->id,
                    'operation_date' => $date,
                    'total_hours' => 12,
                ]);

                TaskProductionStockKeepingUnit::create([
                    'task_p_id' => $task_production_plan->id,
                    'sku_id' => $sku->id,
                    'tarimas' => $row['tarimas']
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    private function createWeeklyProductionPlan($week, $year)
    {
            $weekly_production_plan = WeeklyProductionPlan::create([
                'year' => $year,
                'week' => $week
            ]);

        return $weekly_production_plan;
    }
}
