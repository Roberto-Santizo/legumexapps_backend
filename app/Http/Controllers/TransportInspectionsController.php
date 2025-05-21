<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransportInspectionRequest;
use App\Http\Resources\TransportInspectionResource;
use App\Models\TransportCondition;
use App\Models\TransportInspection;
use App\Models\TransportInspectionCondition;
use App\Models\TransportInspectionRmReception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function store(CreateTransportInspectionRequest $request)
    {
        $data = $request->validated();
        $signature2 = $data['verify_by_signature'];
        $user = $request->user();

        try {
            list(, $signature2) = explode(',', $signature2);
            $signature2 = base64_decode($signature2);
            $filename2 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename2, $signature2);

            $transport_inspection = TransportInspection::create([
                'planta_id' => $data['planta_id'],
                'product_id' => $data['product_id'],
                'pilot_name' => $data['pilot_name'],
                'truck_type' => $data['truck_type'],
                'plate' => $data['plate'],
                'observations' => $data['observations'] ?? '',
                'quality_manager_signature' => '',
                'verify_by_signature' => $filename2,
                'user_id' => $user->id,
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

            foreach ($data['boletas'] as $boleta) {
                TransportInspectionRmReception::create([
                    'transport_id' => $transport_inspection->id,
                    'reception_id' => $boleta['id']
                ]);
            }

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
