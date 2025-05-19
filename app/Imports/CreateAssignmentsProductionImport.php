<?php

namespace App\Imports;

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

    public $tasks;

    public function __construct($id)
    {
        $this->tasks = TaskProductionPlan::where('line_id', $id)->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($this->tasks as $task) {
                try {
                    $position = LinePosition::where('line_id', $task->line_id)->where('name', $row['posicion'])->first();

                    if (!$position) {
                        throw new Exception("La posición " . $row['posicion'] . " no existe");
                    }

                    TaskProductionEmployee::create([
                        'task_p_id' => $task->id,
                        'name' => $row['nombre'],
                        'code' => $row['codigo'],
                        'position' => $row['posicion']
                    ]);
                    $task->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
    }
}
