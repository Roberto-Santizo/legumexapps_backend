<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransportInspectionResource;
use App\Models\RmReception;
use App\Models\TransportCondition;
use App\Models\TransportInspection;
use App\Models\TransportInspectionCondition;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportInspectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TransportInspectionResource::collection(TransportInspection::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'planta_id' => 'required',
            'product_id' => 'required',
            'rm_reception_id' => 'required',
            'pilot_name' => 'required',
            'truck_type' => 'required',
            'plate' => 'required',
            'observations' => 'sometimes|string|nullable',
            'conditions' => 'required'
        ]);

        $rm_reception = RmReception::find($data['rm_reception_id']);

        if(!$rm_reception){
            return response()->json([
                'msg' => 'Not Found'
            ],500);
        }

        try {
            $transport_inspection = TransportInspection::create([
                'planta_id' => $data['planta_id'],
                'rm_reception_id' => $rm_reception->id,
                'product_id' => $data['product_id'],
                'pilot_name' => $data['pilot_name'],
                'truck_type' => $data['truck_type'],
                'plate' => $data['plate'],
                'observations' => $data['observations'] ?? '',
                'date' => Carbon::now()
            ]);

            foreach ($data['conditions'] as $condition) {
                $conditionModel = TransportCondition::find($condition['id']);

                if(!$conditionModel){
                    return response()->json([
                        'msg' => 'Condition Not Found'
                    ],404);
                }

                TransportInspectionCondition::create([
                    'transport_inspection_id' => $transport_inspection->id,
                    'transport_condition_id' => $conditionModel->id,
                    'status' => $condition['value']
                ]);
            }

            return response()->json([
                'msg' => 'Created Successfully' 
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
