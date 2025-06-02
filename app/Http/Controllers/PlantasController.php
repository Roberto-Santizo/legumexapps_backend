<?php

namespace App\Http\Controllers;

use App\Models\Planta;

class PlantasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plantas = Planta::all();
        return response()->json([
            'data' => $plantas
        ]);
    }
}
