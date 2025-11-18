<?php

namespace App\Imports;

use App\Models\Tarea;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class TasksImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                if (empty($row['codigo']) || empty($row['tarea'])) {
                    continue;
                }

                $exists = Tarea::where('code', $row['codigo'])->first();

                if ($exists != null) {
                    throw new Exception('La tarea ya existe');
                }

                Tarea::create([
                    'code' => $row['codigo'],
                    'name' => $row['tarea'],
                    'description' => $row['descripcion'] ?? 'SIN DESCRIPCIÓN'
                ]);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
