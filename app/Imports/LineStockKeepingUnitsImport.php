<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LineStockKeepingUnits;
use App\Models\StockKeepingUnit;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf;

class LineStockKeepingUnitsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $line = Line::where('code', $row['linea'])->first();
            $sku = StockKeepingUnit::where('code', $row['sku'])->first();

            if (!$line) {
                throw new Exception("Linea no encontrada " . $row['linea']);
            }

            if (!$sku) {
                throw new Exception("SKU no encontrado " . $row['sku']);
            }

            try {
                LineStockKeepingUnits::create([
                    'sku_id' => $sku->id,
                    'line_id' => $line->id,
                    'lbs_performance' => $row['rendimiento'],
                    'accepted_percentage' => $row['porcentaje'],
                    'payment_method' => $row['pago'] === 'R' ? 0 : 1

                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
