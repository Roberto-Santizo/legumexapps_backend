<?php

namespace App\Http\Controllers;

use App\Http\Resources\FincaCollection;
use App\Http\Resources\LoteCollection;
use App\Models\Finca;
use App\Models\Lote;
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

    public function show(string $id)
    {
        $lotes = Lote::where('finca_id', $id)->get();
        return new LoteCollection($lotes);
    }
}
