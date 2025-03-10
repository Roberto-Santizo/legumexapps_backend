<?php

namespace App\Console\Commands;

use App\Models\BiometricDepartment;
use App\Models\BiometricEmployee;
use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AssignEmployeesProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-employees-production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign people to production tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $week = Carbon::now()->weekOfYear;
        $year = Carbon::now()->year;
        $tasks_production = TaskProductionPlan::whereHas('weeklyPlan', function ($query) use ($week, $year) {
            $query->where('week', $week);
            $query->where('year', $year);
        })->get();

        foreach ($tasks_production as $task) {
            $line = $task->line->code;
            $biometric_line = BiometricDepartment::where('code', $line)->first();
            $employees = BiometricEmployee::where('auth_dept_id', $biometric_line->id)->get();

            foreach ($employees as $employee) {
                TaskProductionEmployee::create([
                    'task_p_id' => $task->id,
                    'name' => $employee->name,
                    'code' => $employee->last_name,
                    'position' => $employee->pin
                ]);
            }
        }
    }
}
