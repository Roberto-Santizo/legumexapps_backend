<?php

namespace App\Http\Controllers;

use App\Models\DraftWeeklyProductionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WeeklyProductionPlanDraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'week' => 'required'
        ]);

        try {
            $draft = DraftWeeklyProductionPlan::create([
                'week' => $data['week'],
                'year' => Carbon::now()->year,
            ]);

            return response()->json($draft->id);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
            
            return response()->json($draft);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
