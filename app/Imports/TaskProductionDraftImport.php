<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LineStockKeepingUnits;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionDraft;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TaskProductionDraftImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $draft;

    public function __construct($draft)
    {
        $this->draft = $draft;
    }

    public function collection(Collection $rows)
    {
        $skus = StockKeepingUnit::all()->keyBy('code');
        $lines = Line::all()->keyBy('code');
        $performances = LineStockKeepingUnits::all();

        foreach ($rows as $row) {
            if (empty($row['sku'])) {
                continue;
            }

            $sku = $skus->get($row['sku']);

            if (!$sku) {
                throw new Exception("Sku no encontrado " . $row['sku']);
            }

            $line = $lines->get($row['linea']);

            if (!$line) {
                throw new Exception("Linea no encontrada.");
            }

            $performance = $performances->where('line_id', $line->id)->where('sku_id', $sku->id)->first();

            if (!$performance) {
                throw new Exception("Rendimiento no encontrado " . $row['linea']);
            }

            TaskProductionDraft::create([
                'draft_weekly_production_plan_id' => $this->draft->id,
                'line_id' => $line->id,
                'stock_keeping_unit_id' => $sku->id,
                'total_lbs' => $row['libras'],
                'destination' => $row['destino']
            ]);
        }
    }
}
