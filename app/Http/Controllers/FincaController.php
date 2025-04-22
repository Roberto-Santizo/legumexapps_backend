<?php

namespace App\Http\Controllers;

use App\Http\Resources\FincaCollection;
use App\Models\Finca;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new FincaCollection(Finca::all());
    }
}
