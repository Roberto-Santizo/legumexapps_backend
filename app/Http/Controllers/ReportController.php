<?php

namespace App\Http\Controllers;

use App\Exports\InsumosExport;
use App\Exports\PlanillaProductionExport;
use App\Exports\WeeklyPlanExport;
use App\Models\Line;
use App\Models\TaskProductionPlan;
use App\Models\WeeklyPlan;
use App\Models\WeeklyProductionPlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function DownloadReport(Request $request)
    {
        $data = $request->validate([
            'data' => 'required'
        ]);

        $fileName = 'Reporte Plan Semanal.xlsx';
        try {
            $file = Excel::raw(new WeeklyPlanExport($data['data']), \Maatwebsite\Excel\Excel::XLSX);
            return response()->json([
                'fileName' => $fileName,
                'file' => base64_encode($file)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function DownloadReportInsumos(string $id)
    {
        $weekly_plan = WeeklyPlan::find($id);
        $fileName = 'Reporte Insumo.xlsx';
        try {
            $file = Excel::raw(new InsumosExport($weekly_plan), \Maatwebsite\Excel\Excel::XLSX);
            return response()->json([
                'fileName' => $fileName,
                'file' => base64_encode($file)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function PlanillaProduccion(string $weekly_production_plan, string $line_id)
    {
        $weekly_plan_production = WeeklyProductionPlan::find($weekly_production_plan);
        $line = Line::find($line_id);

        if (!$weekly_plan_production) {
            return response()->json([
                'msg' => 'Weekly Production Plan Not Found'
            ], 404);
        }

        if (!$line) {
            return response()->json([
                'msg' => 'Line Not Found'
            ], 404);
        }

        try {
            $tasks = TaskProductionPlan::where('weekly_production_plan_id', $weekly_plan_production->id)->where('line_id', $line->id)->whereNot('end_date', null)->get();
            $file = Excel::raw(new PlanillaProductionExport($tasks, $line), \Maatwebsite\Excel\Excel::XLSX);
            $fileName = 'PLANILLA' . ' ' . $line->code . ' S' . $weekly_plan_production->week;
            return response()->json([
                'fileName' => $fileName,
                'file' => base64_encode($file)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
