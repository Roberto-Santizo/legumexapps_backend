<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlatesResource;
use App\Models\Plate;
use Illuminate\Http\Request;

class PlatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $plates = Plate::paginate(10);
        } else {
            $plates = Plate::all();
        }

        return PlatesResource::collection($plates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:plates,name',
            'carrier_id' => 'required'
        ]);

        try {
            Plate::create($data);

            return response()->json('Placa Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
