<?php

namespace App\Exports;

use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyPlanExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnFormatting, WithMultipleSheets
{

    protected $weekly_plans_id;

    public function __construct($weekly_plans_id)
    {
        $this->weekly_plans_id = $weekly_plans_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rows = collect();
        Carbon::setLocale('es');

        try {
            foreach ($this->weekly_plans_id as $weekly_plan_id) {
                $weekly_plan = WeeklyPlan::find($weekly_plan_id);
                foreach ($weekly_plan->tasks as $task) {
                    if ($task->end_date) {
                        $diff_hours = 0;
                        $performace = 0;
                        if (!$task->closures->isEmpty()) {
                            foreach ($task->closures as $closure) {
                                $diff_hours += $closure->start_date->diffInHours($closure->end_date);
                            }
                        }
                        $start_date = $task->start_date;
                        $end_date = $task->end_date;
                        $performace = $start_date->diffInHours($end_date) - $diff_hours;
                    }

                    $rows->push([
                        'FINCA' => $weekly_plan->finca->name,
                        'SEMANA CALENDARIO' => $weekly_plan->week,
                        'LOTE' => $task->lotePlantationControl->lote->name,
                        'CODIGO TAREA' => $task->task->code,
                        'TAREA' => $task->task->name,
                        'EXTRAORDINARIA' => ($task->extraordinary) ?  'EXTRAORDINARIA' : 'PLANIFICADA',
                        'ESTADO' => ($task->end_date) ? 'CERRADA' : 'ABIERTA',
                        'FECHA DE INICIO' => ($task->start_date) ? $task->start_date->format('d-m-Y h:i:s') : '',
                        'FECHA DE CIERRE' => ($task->end_date) ? $task->end_date->format('d-m-Y h:i:s') : '',
                        'HORA RENDIMIENTO TEORICO' => $task->hours,
                        'HORA RENDIMIENTO REAL' => ($task->end_date) ? $performace : '',
                        'RENDIMIENTO' => ($task->end_date) ? (($task->hours / $performace) * 100) : '',
                        'ATRASADA' => ($task->weeklyPlanChanges->count() > 0) ? 'ATRASADA' : 'PLANIFICADA',
                        'SEMANA ORIGEN' => ($task->weeklyPlanChanges->count() > 0) ? $task->weeklyPlanChanges->last()->WeeklyPlanOrigin->week : 'PLANIFICADA',
                    ]);
                }

                foreach ($weekly_plan->tasks_crops as $task_crop) {
                    foreach ($task_crop->assigments as $assignment) {
                        if ($assignment->end_date) {
                            $emplooyes = $assignment->employees->count();
                            $reported_hours = $assignment->start_date->diffInHours($assignment->end_date);
                            $rendimiento_teorico = ($reported_hours * $emplooyes);
                            $rendimiento_real = $assignment->plants / 120;
                            
                            $rows->push([
                                'FINCA' => $weekly_plan->finca->name,
                                'SEMANA CALENDARIO' => $weekly_plan->week,
                                'LOTE' => $task_crop->lotePlantationControl->lote->name,
                                'CODIGO TAREA' => $task_crop->task->code,
                                'TAREA' => $task_crop->task->name,
                                'EXTRAORDINARIA' => '',
                                'ESTADO' => ($task_crop->status) ? 'CERRADA' : 'ABIERTA',
                                'FECHA DE INICIO' => ($assignment->start_date) ? $assignment->start_date->format('d-m-Y h:i:s') : 'SIN ASIGNACION',
                                'FECHA DE CIERRE' => ($assignment->end_date) ? $assignment->end_date->format('d-m-Y h:i:s') : 'SIN CIERRE',
                                'HORA RENDIMIENTO TEORICO' => $rendimiento_teorico,
                                'HORA RENDIMIENTO REAL' => $rendimiento_real,
                                'RENDIMIENTO' => ($assignment->end_date) ? ($rendimiento_real / ($rendimiento_teorico)) : '0',
                                'ATRASADA' => '',
                                'SEMANA ORIGEN' => '',
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['FINCA', 'SEMANA CALENDARIO', 'LOTE', 'CODIGO TAREA', 'TAREA', 'PLAN', 'ESTADO', 'FECHA DE INICIO', 'FECHA DE CIERRE', 'HORAS RENDIMIENTO TEORICO', 'HORAS RENDIMIENTO REAL', 'RENDIMIENTO', 'ATRASADA', 'SEMANA ORIGEN'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '5564eb'],
            ],
        ]);
    }

    public function sheets(): array
    {
        return [
            new WeeklyPlanExport($this->weekly_plans_id),
            new EmployeeTaskDetailExport($this->weekly_plans_id)
        ];
    }

    public function title(): string
    {
        return 'General Tareas Finca';
    }

    public function columnFormats(): array
    {
        return [
            'L' => '0.00%',
        ];
    }
}
