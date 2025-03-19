<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionPlan;
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
        $year = Carbon::now()->year;
        $week = Carbon::now()->weekOfYear + 1;
        $weekly_production_plan = WeeklyProductionPlan::firstOrCreate(
            [
                'week' => $week,
                'year' => $year
            ]
        );
        foreach ($rows as $row) {
            if (empty($row['linea'])) {
                return null;
            }
            $date = Date::excelToDateTimeObject($row['fecha_de_operacion']);
            $line = Line::where('code', $row['linea'])->first();
            $sku = StockKeepingUnit::where('code', $row['sku'])->first();

            if (!$line) {
                throw new Exception("Line Not Found");
            }

            if (!$sku) {
                throw new Exception("SKU Not Found");
            }

            try {
                TaskProductionPlan::create([
                    'line_id' => $line->id,
                    'weekly_production_plan_id' => $weekly_production_plan->id,
                    'operation_date' => $date,
                    'total_hours' => 12,
                    'sku_id' => $sku->id,
                    'tarimas' => $row['tarimas'],
                    'status' => 0
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
