<?php

namespace App\Http\Controllers;

use App\Exports\InsumosExport;
use App\Exports\WeeklyPlanExport;
use App\Models\WeeklyPlan;
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
}
