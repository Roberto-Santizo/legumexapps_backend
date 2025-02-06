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
                    if ($task->closures->count() > 0) {
                        $this->employeesDistribution($task, $rows);
                    } else {
                        $this->processTask($task, $rows);
                    }
                }
                // else {
                //     $this->transformTaskDro($tarea, $rows);
                // }
            });

            // $weekly_plan->tasks_crops->each(function ($task_crop) use ($rows) {
            //     $this->transformTaskCosecha($task_crop, $rows);
            // });
        }


        return $rows;
    }

    public function employeesDistribution($task, &$rows)
    {
        $dates = [];
        $diff_hours = 0;
        foreach ($task->closures as $index => $closure) {
            $dates[$index][] = $closure->start_date;
            $dates[$index][] = $closure->end_date;
        }
       
        $all_dates = array_merge(...$dates);
        array_unshift($all_dates, $task->start_date);
        array_push($all_dates, $task->end_date);

        $groupedByDay = array_reduce($all_dates, function ($carry, $datetime) {
            $date = $datetime->format('d-m-Y'); 
            $carry[$date][] = $datetime->toDateTimeString();
            return $carry;
        }, []);

        foreach ($groupedByDay as $key => $dates) {
            $first_date = Carbon::parse($dates[0]);
            $second_date = Carbon::parse($dates[1]);
            $total_hours = $first_date->diffInHours($second_date);
            $groupedByDay[$key] = $total_hours;
        }

        dd($groupedByDay);
        // $groupedByDay->map(function($dates){
        //     dd($dates);
        // });
        // $total_hours = ($task->start_date->diffInHours($task->end_date)) - $diff_hours;



        // $rows->push([
        //     'CODIGO' => $employeeAssignment->code,
        //     'EMPLEADO' => $employeeAssignment->name,
        //     'LOTE' => $task->lotePlantationControl->lote->name,
        //     'TAREA REALIZADA' => $task->task->name,
        //     'PLAN' => $task->extraordinary ? 'EXTRAORDINARIA' : 'PLANIFICADA',
        //     'MONTO' => $task->end_date ? ($task->budget / $task->employees->count()) : 0,
        //     'HORAS TOTALES' => $task->end_date ? ($task->hours / $task->employees->count()) : 0,
        //     'ENTRADA' => $registrations['entrance'],
        //     'SALIDA' => $registrations['exit'],
        //     'DIA' => $day
        // ]);
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
                'ENTRADA' => $registrations['entrance'],
                'SALIDA' => $registrations['exit'],
                'DIA' => $day
            ]);
        }
    }


    public function getEmployeeRegistration($emp_id, $date)
    {
        $entrance_date = Employee::whereDate('punch_time', $date)->where('emp_id', $emp_id)->orderBy('punch_time', 'ASC')->first();
        $exit_date = Employee::whereDate('punch_time', $date)->where('emp_id', $emp_id)->orderBy('punch_time', 'DESC')->first();

        return [
            'entrance' => $entrance_date->punch_time->format('d-m-Y h:i:s A'),
            'exit' => $exit_date->punch_time->format('d-m-Y h:i:s A')
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
