<?php

namespace App\Imports;

use App\Models\Crop;
use App\Models\PlantationControl;
use App\Models\Recipe;
use Exception;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CDPSImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                if (empty($row['cdp'])) {
                    continue;
                }

                $crop = Crop::where('name',$row['cultivo'])->get()->first();
                $recipe = Recipe::where('name',$row['receta'])->get()->first();
                $start_date =  Date::excelToDateTimeObject($row['fecha']); 
                PlantationControl::create([
                    'name' => $row['cdp'],
                    'crop_id' => $crop->id,
                    'recipe_id' => $recipe->id,
                    'density' => $row['densidad'],
                    'size' => $row['tamano'],
                    'start_date' => $start_date,
                ]);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}

// CDP	CULTIVO	RECETA	DENSIDAD	TAMAÑO	FECHA DE INICIO

