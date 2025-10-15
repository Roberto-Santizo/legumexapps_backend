<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskGuidelineRequest;
use App\Http\Resources\TaskGuidelineCollection;
use App\Http\Resources\TaskGuidelineResource;
use App\Models\TaskGuideline;
use Illuminate\Http\Request;

class TaskGuidelinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaskGuideline::query();

        try {
            if ($request->query('week')) {
                $query->where('week', $request->query('week'));
            }

            if ($request->query('recipe')) {
                $query->where('recipe_id', $request->query('recipe'));
            }

            if ($request->query('variety')) {
                $query->where('variety_id', $request->query('variety'));
            }

            $limit = $request->query('limit');

            if ($limit) {
                return new TaskGuidelineCollection($query->paginate($limit));
            } else {
                return new TaskGuidelineCollection($query->get());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskGuidelineRequest $request)
    {
        $data = $request->validated();

        try {
            TaskGuideline::create($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Guía de tarea creada correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateTaskGuidelineRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            $task_guideline = TaskGuideline::find($id);

            if (!$task_guideline) {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Tarea no Encontrada"
                ], 404);
            }

            $task_guideline->update($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Guía de tarea actualizada correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }
}
