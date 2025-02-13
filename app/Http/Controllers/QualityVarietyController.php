<?php

namespace App\Http\Controllers;

use App\Http\Resources\QualityVarietyResource;
use App\Models\QualityVariety;
use Illuminate\Http\Request;

class QualityVarietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $varieties = QualityVariety::paginate(10);
        return QualityVarietyResource::collection($varieties);
    }

    public function GetAllVarieties()
    {
        $varieties = QualityVariety::all();
        return QualityVarietyResource::collection($varieties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        QualityVariety::create([
            'name' => $data['name']
        ]);

        return response()->json([
            'message' => 'Created Successfully'
        ]);
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
