<?php

namespace App\Http\Controllers;

use App\Exports\FincaPlanillaExport;
use App\Exports\InsumosExport;
use App\Exports\PackingMaterialNecessityExport;
use App\Exports\PlanillaProductionExport;
use App\Exports\WeeklyPlanExport;
use App\Exports\WeeklyProductionExport;
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

    public function DownloadReportPlanilla(string $id)
    {
        $weekly_plan = WeeklyPlan::find($id);

        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'El Plan Semana no Existe'
            ], 404);
        }

        try {
            $file = Excel::raw(new FincaPlanillaExport($weekly_plan), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = 'Planilla Semana ' . $weekly_plan->week . '.xlsx';

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
            $tasks = TaskProductionPlan::where('weekly_production_plan_id', $weekly_plan_production->id)->where('line_id', $line->id)->whereNotNull('end_date')->get();
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

    public function downloadPackingMaterialNecessity(Request $request, string $id)
    {
        $weekly_production_plan = WeeklyProductionPlan::find($id);

        if (!$weekly_production_plan) {
            return response()->json([
                'msg' => 'El Plan Semanal no Existe'
            ], 404);
        }

        try {
            $file = Excel::raw(new PackingMaterialNecessityExport($weekly_production_plan), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = 'Necesidad Material Empaque S' . $weekly_production_plan->week . '.xlsx';

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

    public function downloadWeeklyProduction(Request $request, string $id)
    {
        $weekly_production_plan = WeeklyProductionPlan::find($id);

        if (!$weekly_production_plan) {
            return response()->json([
                'msg' => 'El Plan Semanal no Existe'
            ], 404);
        }

        try {
            $file = Excel::raw(new WeeklyProductionExport($weekly_production_plan), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = 'Programación Producción S' . $weekly_production_plan->week . '.xlsx';

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
