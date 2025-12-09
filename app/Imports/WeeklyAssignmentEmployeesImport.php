<?php

namespace App\Imports;

use App\Models\Lote;
use App\Models\WeeklyAssignmentEmployee;
use App\Models\WeeklyPlan;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WeeklyAssignmentEmployeesImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $weekly_plan;

    public function __construct($id)
    {
        $this->weekly_plan = WeeklyPlan::find($id);
    }

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {
                if (empty($row['codigo'])) {
                    continue;
                }

                $lote = Lote::where('name', $row['lote'])->first();

                if (!$lote) {
                    throw new HttpException(404, "Lote {$row['lote']} no encotrado");
                }

                $exists = WeeklyAssignmentEmployee::where('code', $row['codigo'])->where('weekly_plan_id', $this->weekly_plan->id)->first();

                if ($exists) {
                    $exists->lote_id = $lote->id;
                    $exists->save();
                    continue;
                }

                WeeklyAssignmentEmployee::create([
                    'lote_id' => $lote->id,
                    'code' => $row['codigo'],
                    'name' => $row['nombre'],
                    'weekly_plan_id' => $this->weekly_plan->id
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "statusCode" => 500,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
