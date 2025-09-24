<?php

namespace App\Console\Commands;

use App\Models\EmployeePaymentWeeklySummary;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CalculateWeeklyPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-weekly-payment {--id= : ID del plan semanal}';
    protected $id;
    protected $entries;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cálcula pago y horas de empleados de finca semanales';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->id = $this->option('id');

        if (!$this->id) {
            $this->error('Debe proporcionar el ID con --id=');
            return;
        }
        
        $weekly_plan = WeeklyPlan::find($this->id);
        if (!$weekly_plan) return;

        if($weekly_plan->summaries->count() > 0){
            $weekly_plan->summaries()->delete();
        }
        
        $startOfWeek = Carbon::now()->setISODate($weekly_plan->year, $weekly_plan->week)->startOfWeek();
        $endOfWeek = Carbon::now()->setISODate($weekly_plan->year, $weekly_plan->week)->endOfWeek();
        $url = env('BIOMETRICO_URL') . "/transactions/{$weekly_plan->finca->terminal_id}";
        $entries = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url, ['start_date' => $startOfWeek->format('Y-m-d'), 'end_date' => $endOfWeek->format('Y-m-d')]);
        $this->entries = $entries->collect();

        $tasks = $weekly_plan->tasks()->whereNotNull('end_date')->where('use_dron', false)->with('employees')->get();
        $harvest_tasks = $weekly_plan->tasks_crops()->with('assigments')->get();

        foreach ($tasks as $task) {
            if ($task->closures->count() > 0) {
                $this->calculateTasksWithClosures($task);
            } else {
                $this->calculateTasksWithNoClosures($task);
            }
        }

        foreach ($harvest_tasks as $task) {
            foreach ($task->assigments as $assignment) {
                if ($assignment->lbs_planta) {
                    $this->calculateHarvestTask($assignment);
                }
            }
        }
    }

    public function calculateTasksWithNoClosures($task)
    {
        try {
            $theorical_hours = $task->hours / $task->employees->count();
            $hours = $task->start_date->diffInHours($task->end_date);
            $amount = $task->budget / $task->employees->count();

            foreach ($task->employees as $employee) {
                EmployeePaymentWeeklySummary::create([
                    'code' => $employee->code,
                    'name' => $employee->name,
                    'emp_id' => $employee->employee_id,
                    'hours' => $hours,
                    'amount' => $amount,
                    'task_weekly_plan_id' => $task->id,
                    'weekly_plan_id' => $this->id,
                    'date' => $task->start_date,
                    'theorical_hours' => $theorical_hours
                ]);
            }
        } catch (\Throwable $th) {
            throw new Exception("Error en la tarea {$task->id}. Error: {$th->getMessage()}");
        }
    }

    public function calculateTasksWithClosures($task)
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
                    $flag = count($this->getEmployeeRegistration($employeeAssignment->code, $day)) > 1;
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
                foreach ($employeeAssignment->dates as $day => $hours) {
                    $percentage = array_sum($hours) / $total_hours;
                    $date = Carbon::parse($day);
                    EmployeePaymentWeeklySummary::create([
                        'code' => $employeeAssignment->code,
                        'name' => $employeeAssignment->name,
                        'emp_id' => $employeeAssignment->employee_id,
                        'hours' => ($task->hours * $percentage),
                        'amount' => ($task->budget * $percentage),
                        'task_weekly_plan_id' => $task->id,
                        'weekly_plan_id' => $this->id,
                        'date' => $date,
                        'theorical_hours' => $task->hours / $task->employees()->count()
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw new Exception("Error en la tarea {$task->id}. Error: {$th->getMessage()}");
        }
    }

    public function calculateHarvestTask($assignment)
    {
        foreach ($assignment->employees as $employee) {
            $percentage = $employee->lbs / $assignment->lbs_planta;
            $total_hours = $assignment->plants / 150;
            $hours = $percentage * $total_hours;
            $amount = $hours * 12.728;

            EmployeePaymentWeeklySummary::create([
                'code' => $employee->code,
                'name' => $employee->name,
                'hours' => $hours,
                'amount' => $amount,
                'emp_id' => $employee->employee_id,
                'daily_assignment_id' => $assignment->id,
                'weekly_plan_id' => $this->id,
                'date' => $assignment->start_date,
                'theorical_hours' => 0
            ]);
        }
    }

    public function getEmployeeRegistration($code, $date)
    {
        $employee = $this->entries->firstWhere('code', $code);

        if (!$employee || empty($employee['transactions'])) {
            return [
                'entrance' => '',
                'exit'     => '',
            ];
        }
        
        $records = collect($employee['transactions'])
        ->filter(function ($transaction) use ($date) {
            $punch_time = Carbon::parse($transaction['punch_time'])->format('Y-m-d');
            $date = Carbon::parse($date);
            return $punch_time === $date->format('Y-m-d');
        })
        ->sortBy('punch_time')
        ->values();
        
        return [
            'entrance' => optional($records->first()['punch_time'])
                ? Carbon::parse($records->first()['punch_time'])
                ->timezone('America/Guatemala')
                ->format('d-m-Y h:i:s A')
                : '',
            'exit' => optional($records->last()['punch_time'])
                ? Carbon::parse($records->last()['punch_time'])
                ->timezone('America/Guatemala')
                ->format('d-m-Y h:i:s A')
                : '',
        ];
    }
}
