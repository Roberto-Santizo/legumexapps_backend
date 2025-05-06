<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LineStockKeepingUnits;
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
        foreach ($rows as $row) {

            $year = $row['year'];
            $week = $row['semana'];
            $weekly_production_plan = WeeklyProductionPlan::firstOrCreate(
                [
                    'week' => $week,
                    'year' => $year
                ]
            );


            if (empty($row['linea'])) {
                return null;
            }
            $line = Line::where('code', $row['linea'])->first();
            $sku = StockKeepingUnit::where('code', $row['sku'])->first();
            $sku_line = LineStockKeepingUnits::where('line_id', $line->id)->where('sku_id', $sku->id)->first();

            if (!$line) {
                throw new Exception("La linea " . $row['linea'] . " no existe");
            }

            if (!$sku) {
                throw new Exception("El SKU " . $row['sku'] . " no existe");
            }

            if (!$sku_line) {
                throw new Exception("El SKU " . $row['sku'] . " no coincide con la linea " . $row['linea']);
            }

            try {
                $total_hours = $sku_line->lbs_performance ? $row['libras'] / $sku_line->lbs_performance : null;

                TaskProductionPlan::create([
                    'line_id' => $line->id,
                    'weekly_production_plan_id' => $weekly_production_plan->id,
                    'total_hours' => $total_hours,
                    'line_sku_id' => $sku_line->id,
                    'destination' => $row['destino'],
                    'total_lbs' => $row['libras'],
                    'status' => 0
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
