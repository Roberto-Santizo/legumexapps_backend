<?php

namespace App\Imports;

use Exception;
use App\Models\Lote;
use App\Models\PlantationControl;
use Illuminate\Support\Collection;
use App\Models\LotePlantationControl;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateLotesImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if(empty($row['lote'])){
                return;
            }

            try {
                $end_date_cdp =  Date::excelToDateTimeObject($row['fecha_final']); 
                $lote = Lote::where('name',$row['lote'])->first();
                $cdp = PlantationControl::where('name',$row['cdp'])->first();
                $lote->cdp->status = 0;
                $lote->cdp->save();
                $lote->cdp->cdp->end_date = $end_date_cdp;
                $lote->cdp->cdp->save();
                LotePlantationControl::create([
                    'lote_id' => $lote->id,
                    'plantation_controls_id' => $cdp->id,
                    'status' => 1
                ]);
            } catch (Exception $th) {
                throw new Exception("Hubo un error al actualizar el lote");
            }
           
        }
    }
}
