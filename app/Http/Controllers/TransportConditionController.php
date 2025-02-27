<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransportConditionsResource;
use App\Models\TransportCondition;
use Illuminate\Http\Request;

class TransportConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conditions = TransportCondition::paginate(10);

        return TransportConditionsResource::collection($conditions);
    }

    public function getAllConditions()
    {
        $conditions = TransportCondition::all();

        return TransportConditionsResource::collection($conditions);
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
            TransportCondition::create([
                'name' => $data['name']
            ]);

            return response()->json([
                'msg' => 'Condition Created Successfully'
            ],200);
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
