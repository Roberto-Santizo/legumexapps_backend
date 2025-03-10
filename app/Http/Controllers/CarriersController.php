<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarriersResource;
use App\Models\Carrier;
use Illuminate\Http\Request;

class CarriersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carriers = Carrier::paginate(10);
        return CarriersResource::collection($carriers);
    }

    public function GetAllCarriers()
    {
        $carriers = Carrier::all();

        return CarriersResource::collection($carriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:carriers,code'
        ]);
        
        try {
            Carrier::create($data);

            return response()->json([
                'msg' => 'Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
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
