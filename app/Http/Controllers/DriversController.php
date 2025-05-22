<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriversResource;
use App\Models\Carrier;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriversController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $drivers = Driver::paginate(10);
        } else {
            $drivers = Driver::all();
        }
        return DriversResource::collection($drivers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'dpi' => 'nullable|unique:drivers,dpi',
            'license' => 'nullable|unique:drivers,license',
            'carrier_id' => 'required'
        ]);

        try {
            $carrier = Carrier::find($data['carrier_id']);

            if (!$carrier) {
                return response()->json([
                    'msg' => 'Carrier Not Found'
                ], 404);
            }

            Driver::create([
                'name' => $data['name'],
                'dpi' => $data['dpi'] ?? null,
                'license' => $data['license'] ?? null,
                'carrier_id' => $carrier->id
            ]);

            return response()->json('Piloto Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
