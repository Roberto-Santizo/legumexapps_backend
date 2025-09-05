<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyProductionPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CreateAssignmentsProductionImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $weekly_plan;

    public function __construct($id)
    {
        $this->weekly_plan = WeeklyProductionPlan::find($id);
    }


    public function collection(Collection $rows)
    {
        $rowsGroupedByDepartment = $rows->groupBy('departamento');
        foreach ($rowsGroupedByDepartment as $line => $assigments) {
            try {
                $line = Line::where('code', $line)->first();
                if (empty($line)) {
                    return null;
                }

                if (!$line) {
                    throw new Exception("No existe linea " . $line);
                }

                $tasks = TaskProductionPlan::where('line_id', $line->id)
                    ->where('weekly_production_plan_id', $this->weekly_plan->id)
                    ->whereDoesntHave('employees')
                    ->get();


                foreach ($assigments as $employee) {
                    foreach ($tasks as $task) {
                        TaskProductionEmployee::create([
                            'task_p_id' => $task->id,
                            'name' => $employee['nombre'],
                            'position' => $employee['posicion'],
                            'code' => $employee['codigo']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
