<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductorCDPResource;
use App\Models\Finca;
use App\Models\ProductorPlantationControl;
use Illuminate\Http\Request;

class ProductorPlantationControlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cdps = ProductorPlantationControl::paginate(10);
        return ProductorCDPResource::collection($cdps);
    }

    public function GetAllProductorsCDPS()
    {
        $cdps = ProductorPlantationControl::all();
        return ProductorCDPResource::collection($cdps);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:productor_plantation_controls,name',
            'finca_id' => 'required',
        ]);

        try {

            $finca = Finca::find($data['finca_id']);

            if (!$finca) {
                return response()->json([
                    'msg' => 'Finca not Found'
                ], 404);
            }

            ProductorPlantationControl::create([
                'name' => $data['name'],
                'finca_id' => $finca->id,
                'status' => 1
            ]);

            return response()->json([
                'msg' => 'Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
