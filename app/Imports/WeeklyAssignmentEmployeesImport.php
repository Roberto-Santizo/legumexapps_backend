<?php

namespace App\Imports;

use App\Models\Finca;
use App\Models\Lote;
use App\Models\WeeklyAssignmentEmployee;
use App\Models\WeeklyPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WeeklyAssignmentEmployeesImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $plans;
    public $fincas;

    public function __construct()
    {
        $this->plans = WeeklyPlan::all();
        $this->fincas = Finca::all();
    }

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {
                $finca = $this->fincas->firstWhere('code', $row['finca']);

                if (!$finca) {
                    throw new HttpException(404, "Finca no encontrada para el codigo: " . $row['finca']);
                }

                $plan = $this->plans->where('finca_id', $finca->id)->where('week', $row['semana'])->first();

                if (!$plan) {
                    throw new HttpException(404, "Plan Semanal no encontrado para la finca: " . $row['finca'] . " y semana: " . $row['semana']);
                }

                if (empty($row['codigo'])) {
                    continue;
                }

                WeeklyAssignmentEmployee::create([
                    'code' => $row['codigo'],
                    'name' => $row['nombre'],
                    'weekly_plan_id' => $plan->id
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "statusCode" => 500,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
