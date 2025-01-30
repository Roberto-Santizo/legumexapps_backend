<?php
namespace App\Imports;

use App\Exceptions\ImportExeption;
use App\Models\Tarea;
use Error;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TasksImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                if (empty($row['code']) || empty($row['tarea'])) {
                    continue; 
                }
                Tarea::create([
                    'code' => $row['code'],
                    'name' => $row['tarea'],
                    'description' => $row['descripcion'] ?? 'SIN DESCRIPCIÓN'
                ]);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
            
        }
    }
}
