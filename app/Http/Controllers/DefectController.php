<?php

namespace App\Http\Controllers;

use App\Http\Resources\DefectResource;
use App\Models\Defect;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $defects = Defect::paginate(10);
        return DefectResource::collection($defects);
    }

    public function GetDefectsByProduct(string $id)
    {
        $defects = Defect::where('product_id',$id)->get();
        return DefectResource::collection($defects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'name' => 'required',
        //     'tolerance_percentage' => 'required',
        //     'quality_variety_id' => 'required'
        // ]);

        // $variety = QualityVariety::find($data['quality_variety_id']);

        // if(!$variety){
        //     return response()->json([
        //         'message' => 'Variety Not Found' 
        //     ],404);
        // }
        // Defect::create([
        //     'name' => $data['name'],
        //     'tolerance_percentage' => $data['tolerance_percentage'],
        //     'quality_variety_id' => $variety->id
        // ]);

        // return response()->json([
        //     'message' => 'Created Successfully' 
        // ]);
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
