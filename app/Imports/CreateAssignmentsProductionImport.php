<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LinePosition;
use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function PHPUnit\Framework\isEmpty;

class CreateAssignmentsProductionImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */


    public function collection(Collection $rows)
    {
        $rowsGroupedByDepartment = $rows->groupBy('departamento');
        foreach ($rowsGroupedByDepartment as $line => $assigments) {
            try {
                $line = Line::where('code', $line)->first();

                if (!$line) {
                    throw new Exception("Linea" . $line);
                }

                $tasks = TaskProductionPlan::where('line_id', $line->id)->whereNull('start_date')->get();

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
