<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Error;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeTaskDetailExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
        Carbon::setLocale('es');
        $rows = collect();

        foreach ($this->weekly_plans_id as $weekly_plan_id) {
            $weekly_plan = WeeklyPlan::find($weekly_plan_id);

            $weekly_plan->tasks->each(function ($task) use ($rows) {
                if ($task->start_date && $task->end_date) {
                    if (!$task->use_dron) {
                        if ($task->closures->count() > 0) {
                            $this->employeesDistribution($task, $rows);
                        } else {
                            $this->processTask($task, $rows);
                        }
                    } else {
                        $this->processTaskDron($task, $rows);
                    }
                }
            });

            $weekly_plan->tasks_crops->each(function ($task_crop) use ($rows) {
                $this->processTaskCrop($task_crop, $rows);
            });
        }


        return $rows;
    }

    public function employeesDistribution($task, &$rows)
    {
        $dates = [];

        try {
            foreach ($task->closures as $index => $closure) {
                $dates[$index][] = $closure->start_date;
                $dates[$index][] = $closure->end_date;
            }
    
            $all_dates = array_merge(...$dates);
            array_unshift($all_dates, $task->start_date);
            array_push($all_dates, $task->end_date);
    
            $groupedByDay = array_reduce($all_dates, function ($carry, $datetime) {
                $date = $datetime->format('Y-m-d');
                $carry[$date][] = $datetime->toDateTimeString();
                return $carry;
            }, []);
    
            foreach ($groupedByDay as $key => $dates) {
                $first_date = Carbon::parse($dates[0]);
                $second_date = Carbon::parse($dates[1]);
                $total_hours = $first_date->diffInHours($second_date);
                $groupedByDay[$key] = $total_hours;
            }
    
            $task->employees->map(function ($employeeAssignment) use ($groupedByDay) {
                $employeeAssignment->total_hours = 0;
                $employeeAssignment->dates = [];
                $dates = [];
                foreach ($groupedByDay as $day => $hours) {
                    $flag = count($this->getEmployeeRegistration($employeeAssignment->employee_id, $day)) > 1;
                    if ($flag) {
                        $dates[$day][] = $hours;
                        $employeeAssignment->dates = $dates;
                        $employeeAssignment->total_hours += $hours;
                    }
                }
    
                return $employeeAssignment;
            });
    
    
            $task->employees->map(function ($employeeAssignment) use ($groupedByDay, $rows, $task) {
                foreach ($employeeAssignment->dates as $day => $hours) {
                    $total_hours = $task->employees->reduce(function ($carry, $task) {
                        return $carry + array_sum(array_merge(...array_values($task->dates ?? [])));
                    }, 0);
    
                    $percentage = $hours[0] / $total_hours;
                    $day_carbon = Carbon::parse($day);
                    $registrations = $this->getEmployeeRegistration($employeeAssignment->employee_id, $day_carbon);
                    $rows->push([
                        'CODIGO' => $employeeAssignment->code,
                        'EMPLEADO' => $employeeAssignment->name,
                        'LOTE' => $task->lotePlantationControl->lote->name,
                        'TAREA REALIZADA' => $task->task->name,
                        'PLAN' => $task->extraordinary ? 'EXTRAORDINARIA' : 'PLANIFICADA',
                        'MONTO' => $percentage * $task->budget,
                        'HORAS TOTALES' => $percentage * $total_hours,
                        'ENTRADA' => $registrations['entrance'] ?? '',
                        'SALIDA' => $registrations['exit'] ?? '',
                        'DIA' => $day_carbon->isoFormat('dddd')
                    ]);
                }
            });
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public function processTaskCrop($task, &$rows)
    {
        foreach ($task->assigments as $assignment) {
            if ($assignment->end_date && $assignment->lbs_planta) {
                foreach ($assignment->employees as $employeeAssignment) {
                    $day = $assignment->start_date->IsoFormat('dddd');
                    $percentage = $employeeAssignment->lbs / $assignment->lbs_finca;
                    $total_hours = $assignment->plants/150;
                    $hours = $percentage*$total_hours;
                    $budget = $hours*12.728;
                    $registrations = $this->getEmployeeRegistration($employeeAssignment->employee_id, $assignment->start_date);

                    $rows->push([
                        'CODIGO' => $employeeAssignment->code,
                        'EMPLEADO' => $employeeAssignment->name,
                        'LOTE' => $task->lotePlantationControl->lote->name,
                        'TAREA REALIZADA' => $task->task->name,
                        'PLAN' => $task->extraordinary ? 'EXTRAORDINARIA' : 'PLANIFICADA',
                        'MONTO' => $budget,
                        'HORAS TOTALES' => $hours,
                        'ENTRADA' => $registrations['entrance'] ?? '',
                        'SALIDA' => $registrations['exit'] ?? '',
                        'DIA' => $day
                    ]);
                }
            }
        }

        return $rows;
    }

    public function processTaskDron($task, &$rows)
    {
        $rows->push([
            'CODIGO' => '',
            'EMPLEADO' => 'DRON',
            'LOTE' => $task->lotePlantationControl->lote->name,
            'TAREA REALIZADA' => $task->task->name,
            'PLAN' => $task->extraordinary ? 'EXTRAORDINARIA' : 'PLANIFICADA',
            'MONTO' => '',
            'HORAS TOTALES' => $task->start_date->diffInHours($task->end_date),
            'ENTRADA' => '',
            'SALIDA' => '',
            'DIA' => $task->start_date->IsoFormat('dddd')
        ]);
    }

    public function processTask($task, &$rows)
    {
        $day = $task->end_date ? $task->start_date->isoFormat('dddd') : '';

        foreach ($task->employees as $employeeAssignment) {
            $registrations = $this->getEmployeeRegistration($employeeAssignment->employee_id, $task->start_date);
            $rows->push([
                'CODIGO' => $employeeAssignment->code,
                'EMPLEADO' => $employeeAssignment->name,
                'LOTE' => $task->lotePlantationControl->lote->name,
                'TAREA REALIZADA' => $task->task->name,
                'PLAN' => $task->extraordinary ? 'EXTRAORDINARIA' : 'PLANIFICADA',
                'MONTO' => $task->end_date ? ($task->budget / $task->employees->count()) : 0,
                'HORAS TOTALES' => $task->end_date ? ($task->hours / $task->employees->count()) : 0,
                'ENTRADA' => $registrations['entrance'] ?? '',
                'SALIDA' => $registrations['exit'] ?? '',
                'DIA' => $day
            ]);
        }
    }

    public function getEmployeeRegistration($emp_id, $date)
    {
        $entrance_date = Employee::whereDate('punch_time', $date)->where('emp_id', $emp_id)->orderBy('punch_time', 'ASC')->first();
        $exit_date = Employee::whereDate('punch_time', $date)->where('emp_id', $emp_id)->orderBy('punch_time', 'DESC')->first();

        if (!$entrance_date && !$exit_date) {
            return [];
        }

        return [
            'entrance' => $entrance_date ? $entrance_date->punch_time->format('d-m-Y h:i:s A') : null,
            'exit' => $exit_date ? $exit_date->punch_time->format('d-m-Y h:i:s A') : null
        ];
    }

    public function headings(): array
    {
        return ['CODIGO', 'EMPLEADO', 'LOTE', 'TAREA REALIZADA', 'PLAN', 'MONTO GANADO', 'HORAS TOTALES', 'ENTRADA BIOMETRICO', 'SALIDA BIOMETRICO', 'DIA'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '5564eb']],
        ]);
    }

    public function title(): string
    {
        return 'Detalle Tareas';
    }
}
