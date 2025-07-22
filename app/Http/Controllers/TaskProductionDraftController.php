<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDraftTaskPlan;
use App\Models\DraftWeeklyProductionPlan;
use App\Models\LineStockKeepingUnits;
use App\Models\TaskProductionDraft;
use Illuminate\Http\Request;

class TaskProductionDraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(String $id)
    {
        $draft = DraftWeeklyProductionPlan::find($id);

        if (!$draft) {
            return response()->json([
                'msg' => 'Draft No Encontrado'
            ], 404);
        }

        try {
          
            
            return response()->json('aca');
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDraftTaskPlan $request)
    {
        $data = $request->validated();

        try {
            TaskProductionDraft::create($data);

            return response()->json('Tarea Creada Correctamente', 200);
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
        //
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
