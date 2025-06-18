<?php

namespace App\Imports;

use App\Models\PackingMaterial;
use App\Models\StockKeepingUnit;
use App\Models\StockKeepingUnitRecipe;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RecipeStockKeepingUnitsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $sku = StockKeepingUnit::where('code', $row['sku'])->first();
            $item = PackingMaterial::where('code', $row['codigo_item'])->first();

            if (!$sku) {
                throw new Exception("SKU no encontrado " . $row['sku']);
            }

            if (!$item) {
                throw new Exception("Item no encontrado " . $row['codigo_item']);
            }

            try {
                StockKeepingUnitRecipe::create([
                    'sku_id' => $sku->id,
                    'item_id' => $item->id,
                    'lbs_per_item' => $row['lbs_per_item']
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
