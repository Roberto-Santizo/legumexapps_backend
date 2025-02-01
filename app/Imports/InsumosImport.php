<?php

namespace App\Imports;

use App\Models\Insumo;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InsumosImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        try {
            foreach ($collection as $row) {
                
                if (empty($row['code']) || empty($row['insumo'])) {
                    continue;
                }
                
                Insumo::create([
                    'code' => $row['code'],
                    'name' => $row['insumo'],
                    'measure' => $row['medida']
                ]);
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
