<?php

namespace App\Imports;

use App\Models\PackingMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PackingMaterialImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                PackingMaterial::create([
                    'name' => $row['item'],
                    'description' => $row['descripcion'],
                    'code' => $row['codigo'],
                    'blocked' => false,
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
