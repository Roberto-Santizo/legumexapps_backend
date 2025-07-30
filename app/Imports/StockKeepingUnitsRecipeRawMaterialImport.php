<?php

namespace App\Imports;

use App\Models\RawMaterialItem;
use App\Models\RawMaterialSkuRecipe;
use App\Models\StockKeepingUnit;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockKeepingUnitsRecipeRawMaterialImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $skus = StockKeepingUnit::all();
        $items = RawMaterialItem::all();

        foreach ($rows as $row) {
            if (empty($row['sku'])) {
                continue;
            }

            $sku = $skus->where('code', $row['sku'])->first();

            if (!$sku) {
                throw new Exception("SKU no encontrado " . $row['sku']);
            }

            $item = $items->where('code', $row['codigo'])->first();

            if (!$item) {
                throw new Exception("Item no encontrado " . $row['codigo']);
            }

            RawMaterialSkuRecipe::create([
                'stock_keeping_unit_id' => $sku->id,
                'raw_material_item_id' => $item->id,
                'percentage' => $row['porcentaje']
            ]);
        }
    }
}
