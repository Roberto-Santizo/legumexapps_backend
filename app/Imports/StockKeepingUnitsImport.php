<?php

namespace App\Imports;

use App\Models\StockKeepingUnit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockKeepingUnitsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                StockKeepingUnit::create([
                    'code' => $row['codigo'],
                    'product_name' => $row['product'],
                    'presentation' => $row['presentation'] ?? null,
                    'boxes_pallet' => $row['cajas_pallet'] ?? null,
                    'pallets_container' => $row['pallet_contenedor'] ?? null,
                    'hours_container' => $row['horas_contenedor'] ?? null,
                    'client_name' => $row['cliente'] ?? null,
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
