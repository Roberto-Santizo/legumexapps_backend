<?php

namespace App\Http\Controllers;

use App\Http\Resources\RmReceptionsDiferencePercetageResource;
use App\Models\RmReception;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardCalidad extends Controller
{
    public function ReceptionPedingQuality()
    {
        $rm_receptions = RmReception::where('status', 3)->get();

        return response()->json([
            'total_docs' => $rm_receptions->count()
        ]);
    }


    public function ReceptionByPercentageDiference()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $rm_receptions = RmReception::where('status', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $formattedData = RmReceptionsDiferencePercetageResource::collection($rm_receptions)->toArray(request());

        $dataGroupedByFinca = collect($formattedData)
            ->groupBy('finca')
            ->map(function ($items, $finca) {
                return [
                    'name' => $finca,
                    'field_percentage' => round($items->avg('quality_percentage'), 1), 
                    'quality_percentage' => round($items->avg('field_percentage'), 1),  
                ];
            })->values(); 

        return response()->json([
            'data' => $dataGroupedByFinca
        ]);
    }
}
