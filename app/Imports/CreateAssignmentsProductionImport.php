<?php

namespace App\Imports;

use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionPlan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CreateAssignmentsProductionImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public $tasks;

    public function __construct($id)
    {
        $this->tasks = TaskProductionPlan::where('line_id', $id)->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($this->tasks as $task) {
                TaskProductionEmployee::create([
                    'task_p_id' => $task->id,
                    'name' => $row['nombre'],
                    'code' => $row['codigo'],
                    'position' => $row['posicion']
                ]);
                $task->status = 1;
                $task->save();
            }
        }
    }
}
