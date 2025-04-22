<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecipeCollection;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new RecipeCollection(Recipe::all());
    }
}
