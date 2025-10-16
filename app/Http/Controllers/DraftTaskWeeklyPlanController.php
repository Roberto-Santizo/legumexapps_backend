<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskWeeklyPlanDraftRequest;
use App\Models\TaskWeeklyPlanDraft;

class DraftTaskWeeklyPlanController extends Controller
{

    public function show(string $id)
    {
        try {
            $draft = TaskWeeklyPlanDraft::find($id);

            if (!$draft) {
                return response()->json([
                    "statusCode" =>  404,
                    "message" => "Tarea No Encontrada"
                ], 404);
            }

            return response()->json([
                "statusCode" => 200,
                "data" => [
                    "draft_weekly_plan_id" => $draft->draft_weekly_plan_id,
                    "hours" => $draft->hours,
                    "budget" => $draft->budget,
                    "slots" => $draft->slots,
                    "tags" => $draft->tags
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" =>  500,
                "message" => "Hubo un error"
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateTaskWeeklyPlanDraftRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            $draft = TaskWeeklyPlanDraft::find($id);

            if (!$draft) {
                return response()->json([
                    "statusCode" =>  404,
                    "message" => "Tarea No Encontrada"
                ], 404);
            }

            $draft->update($data);

            return response()->json([
                "statusCode" => 201,
                "message" => "Tarea Actualizada Correctamente"
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" =>  500,
                "message" => "Hubo un error"
            ], 500);
        }
    }
}
