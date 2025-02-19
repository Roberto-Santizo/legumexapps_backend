<?php

namespace App\Http\Controllers;

use App\Http\Resources\VarietyProductResource;
use App\Models\VarietyProduct;
use Illuminate\Http\Request;

class VarietyProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $varieties = VarietyProduct::paginate(10);
        return VarietyProductResource::collection($varieties);
    }

    public function GetAllVarieties()
    {
        $varieties = VarietyProduct::all();
        return VarietyProductResource::collection($varieties);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        try {
            VarietyProduct::create([
                'name' => $data['name']
            ]);

            return response()->json([
                'msg' => 'Created Successfully' 
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage() 
            ],500);
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
