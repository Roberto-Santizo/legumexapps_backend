<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LineStockKeepingUnits;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyProductionPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WeeklyProductionPlanImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];

    public function collection(Collection $rows)
    {
        $filaExcel = 2;

        foreach ($rows as $row) {
            $year = $row['year'];
            $week = $row['semana'];

            $weekly_production_plan = WeeklyProductionPlan::firstOrCreate([
                'week' => $week,
                'year' => $year
            ]);

            $line = Line::where('code', $row['linea'])->first();

            if (!$line) {
                $this->errors[] = "Fila {$filaExcel}: La línea {$row['linea']} no existe";
                $filaExcel++;
                continue;
            }

            $sku = StockKeepingUnit::where('code', $row['sku'])->first();

            if (!$sku) {
                $this->errors[] = "Fila {$filaExcel}: El SKU {$row['sku']} no existe";
                $filaExcel++;
                continue;
            }

            $sku_line = LineStockKeepingUnits::where('line_id', $line->id)
                ->where('sku_id', $sku->id)
                ->first();

            if (!$sku_line) {
                $this->errors[] = "Fila {$filaExcel}: El SKU {$row['sku']} no coincide con la línea {$row['linea']}";
                $filaExcel++;
                continue;
            }

            $total_hours = $sku_line->lbs_performance ? $row['libras'] / $sku_line->lbs_performance : null;

            TaskProductionPlan::create([
                'line_id' => $line->id,
                'weekly_production_plan_id' => $weekly_production_plan->id,
                'total_hours' => $total_hours,
                'line_sku_id' => $sku_line->id,
                'destination' => $row['destino'],
                'total_lbs' => $row['libras'],
                'status' => 1
            ]);
            $filaExcel++;
        }
    }
}
