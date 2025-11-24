<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskInsumoRecipeRequest;
use App\Models\TaskInsumoRecipe;
use Illuminate\Http\Request;

class TaskInsumoRecipeController extends Controller
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
    public function store(CreateTaskInsumoRecipeRequest $request)
    {
        $data = $request->validated();

        try {
            TaskInsumoRecipe::create($data);

            return response()->json([
                "statusCode" => 201,
                "message" => "Receta Creada Correctamente"
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                "message" => "Hubo un error"
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
