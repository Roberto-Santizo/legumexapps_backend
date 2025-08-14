<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\PersonnelEmployee;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class FincaPlanillaExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $weekly_plan;

    public function __construct($weekly_plan)
    {
        $this->weekly_plan = $weekly_plan;
    }

    public function collection()
    {
        $rows = collect();
        $tasks = $this->weekly_plan->tasks()->whereNotNull('end_date')->get();
        $harvest_tasks = $this->weekly_plan->tasks_crops;

        $employees_finca = PersonnelEmployee::select('id', 'first_name', 'last_name', 'emp_code')->get();

        $employees_finca->map(function ($employee) use ($tasks, $harvest_tasks) {
            $employee['hours'] = 0;
            $employee['amount'] = 0;
            foreach ($tasks as $task) {
                $exists = $task->employees()->where('code', $employee->last_name)->exists();

                if ($exists) {
                    if ($task->closures->count() > 0) {
                        $this->calculateTasksWithClosures($employee, $task);
                    } else {
                        $this->calculateTasksWithNoClosures($employee, $task);
                    }
                }
            }

            foreach ($harvest_tasks as $task) {
                foreach ($task->assigments as $assignment) {
                    $employee_assignment = $assignment->employees()->where('code', $employee->last_name)->first();

                    if ($employee_assignment && $assignment->lbs_planta) {
                        $this->calculateHarvestTask($employee, $assignment, $employee_assignment);
                    }
                }
            }
        });

        $employees_finca->map(function ($employee) use ($rows) {
            $septimo = 0;
            $bono = 0;

            if ($employee->hours >= 44) {
                $septimo = ((3097.21 * 12) / 365) * 1.5;
                $bono = ((250 * 12) / 365) * 7;
            }

            $rows->push([
                'CODIGO' => $employee->last_name,
                'EMPLEADO' => $employee->first_name,
                'HORAS' => $employee->hours,
                'MONTO' => $employee->amount,
                'SEPTIMO' => $septimo,
                'BONIFICACIÓN' => $bono,
                'TOTAL A DEVENGAR' => $employee->amount + $septimo + $bono
            ]);
        });

        return $rows;
    }

    public function calculateTasksWithClosures(&$employee, $task)
    {
        try {
            $dates = [];

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

            foreach ($groupedByDay as $key => $dayDates) {
                $first_date = Carbon::parse($dayDates[0]);
                $second_date = Carbon::parse($dayDates[1]);
                $groupedByDay[$key] = $first_date->diffInHours($second_date);
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

            $total_hours = $task->employees->reduce(function ($carry, $emp) {
                return $carry + array_sum(array_merge(...array_values($emp->dates ?? [])));
            }, 0);

            foreach ($task->employees as $employeeAssignment) {
                if ($employeeAssignment->employee_id !== $employee['id']) {
                    continue;
                }

                foreach ($employeeAssignment->dates as $day => $hours) {
                    $percentage = array_sum($hours) / $total_hours;
                    $employee['hours'] += ($task->hours * $percentage);
                    $employee['amount'] += ($task->budget * $percentage);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function calculateTasksWithNoClosures(&$employee, $task)
    {
        $hours = $task->hours / $task->employees->count();
        $amount = $task->budget / $task->employees->count();
        $employee['hours'] += $hours;
        $employee['amount'] += $amount;
    }

    public function calculateHarvestTask(&$employee, $assignment, $employee_assignment)
    {
        $percentage = $employee_assignment->lbs / $assignment->lbs_planta;
        $total_hours = $assignment->plants / 150;
        $hours = $percentage * $total_hours;
        $amount = $hours * 12.728;

        $employee['hours'] += $hours;
        $employee['amount'] += $amount;
    }

    public function calculateTotalHoursByDay($task)
    {
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

        return $groupedByDay;
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
        return ['CODIGO', 'EMPLEADO', 'HORAS SEMANALES', 'MONTO', 'SEPTIMO', 'BONIFICACIÓN', 'TOTAL A DENVEGAR'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '5564eb']],
        ]);
    }

    public function title(): string
    {
        return 'Detalle Tareas';
    }
}
