<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRecipeRequest;
use App\Http\Resources\RecipeCollection;
use App\Models\Recipe;
use Illuminate\Http\Client\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new RecipeCollection(Recipe::all());
    }

    public function store(CreateRecipeRequest $request)
    {
        $data = $request->validated();
        try {
            Recipe::create($data);

            return response()->json([
                "statusCode" => 201,
                "message" => "Receta Creada Correctamete"
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                "message" => "Hubo un error"
            ], 500);
        }
    }
}
