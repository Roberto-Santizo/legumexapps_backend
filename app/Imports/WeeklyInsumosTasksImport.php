<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\TaskInsumos;
use Error;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WeeklyInsumosTasksImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    private $tareasMap;

    public function __construct(&$tareasMap)
    {
        $this->tareasMap = &$tareasMap;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['id_tarea'])) {
                return null;
            }

            try {
                $tareaLote = $this->tareasMap[$row['id_tarea']];
                $insumo = Insumo::where('code', $row['insumo'])->get()->first();
                TaskInsumos::create([
                    'insumo_id' => $insumo->id,
                    'task_weekly_plan_id' => $tareaLote,
                    'assigned_quantity' => $row['cantidad']
                ]);
            } catch (Exception $th) {
                throw new Error($row['insumo']);
            }
        }
    }
}
