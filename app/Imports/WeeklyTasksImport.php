<?php

namespace App\Imports;

use App\Models\Tarea;
use Illuminate\Support\Carbon;
use App\Models\Finca;
use App\Models\Lote;
use App\Models\TaskCrop;
use App\Models\TaskCropWeeklyPlan;
use App\Models\TaskWeeklyPlan;
use App\Models\WeeklyPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WeeklyTasksImport implements ToCollection, WithHeadingRow
{
    private $tareasMap;
    private $weeklyPlans = [];
    private $fincas;
    private $tasks;
    private $tasksCrop;

    public function __construct(&$tareasMap)
    {
        $this->tareasMap = &$tareasMap;
        $this->fincas = Finca::all()->keyBy('code');
        $this->tasks = Tarea::all()->keyBy('code');
        $this->tasksCrop = TaskCrop::all()->keyBy('code');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['id'])) {
                return null;
            }
            $fechaSemanaActual = Carbon::now();
            $fechaSemanaImportada = Carbon::now()->setISODate($row['year'], $row['numero_de_semana']);
            try {
                $finca = $this->fincas[$row['finca']] ?? null;
                if (!$finca) {
                    throw new Exception("Finca con código {$row['finca']} no encontrada.");
                }

                if ($fechaSemanaImportada->isBefore($fechaSemanaActual)) {
                    throw new Exception("La semana indicada es anterior a la semana actual.");
                }

                $lote = Lote::where('name', $row['lote'])->where('finca_id', $finca->id)->first();
                $task = $this->tasks[$row['tarea']] ?? $this->tasksCrop[$row['tarea']];
                $weeklyplan = $this->getOrCreatePlanSemanal($finca->code, $row['numero_de_semana'], $row['year']);
                if ($task instanceof Tarea) {
                   $task = TaskWeeklyPlan::create([
                        'weekly_plan_id' => $weeklyplan->id,
                        'lote_plantation_control_id' => $lote->cdp->id,
                        'tarea_id' => $task->id,
                        'workers_quantity' => max(1, floor($row['horas'] / 8)),
                        'budget' => round($row['presupuesto'], 2),
                        'hours' => round($row['horas'], 2),
                        'slots' => max(1, floor($row['horas'] / 8)),
                        'extraordinary' => false
                    ]);
                
                }else{
                    $task = TaskCropWeeklyPlan::create([
                        'weekly_plan_id' => $weeklyplan->id,
                        'lote_id' => $lote->id,
                        'lote_plantation_control_id' => $lote->cdp->id,
                        'task_crop_id' => $task->id
                    ]);
                }
    
                $this->tareasMap[$row['id']] = $task->id;
            } catch (\Throwable $th) {
                throw new Exception($th->getMessage());
            }
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
