<?php

namespace App\Imports;

use App\Models\TaskProductionDraft;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateDraftWeeklyProductionPlanTasksImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $draft_tasks = TaskProductionDraft::all();

        foreach ($rows as $row) {
            $task = $draft_tasks->where('id', $row['id'])->first();

            if (!$task) {
                continue;
            }

            try {
                $total_lbs = (int)$row['total_libras'];
                $task->total_lbs = $total_lbs;
                $task->destination = $row['destino'];

                $task->update();
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
