<?php

namespace App\Imports;

use App\Models\Finca;
use App\Models\WeeklyPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WeeklyTasksImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    private $weeklyPlans = [];
    private $fincas;

    public function __construct()
    {
        $this->fincas = Finca::all()->keyBy('code');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['id'])) {
                return null;
            }
            $finca = $this->fincas[$row['finca']] ?? null;
            $this->getOrCreatePlanSemanal($finca->code, $row['numero_de_semana'], $row['year']);
        }
    }

    private function getOrCreatePlanSemanal($finca, $numeroSemana, $anio)
    {
        if (isset($this->weeklyPlans[$finca][$numeroSemana])) {
            return $this->weeklyPlans[$finca][$numeroSemana];
        }

        $fincaModel = $this->fincas[$finca] ?? null;
        if (!$fincaModel) {
            throw new Exception("Finca con código {$finca} no encontrada.");
        }

        $planSemanal = WeeklyPlan::firstOrCreate(
            [
                'finca_id' => $fincaModel->id,
                'week' => $numeroSemana,
                'year' => $anio
            ]
        );

        $this->weeklyPlans[$finca][$numeroSemana] = $planSemanal;

        return $planSemanal;
    }
}
