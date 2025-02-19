<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBoletaRMPRequest;
use App\Http\Resources\RmReceptionDetailResource;
use App\Http\Resources\RmReceptionsResource;
use App\Models\Basket;
use App\Models\Defect;
use App\Models\FieldDataReception;
use App\Models\ProdDataReception;
use App\Models\Product;
use App\Models\QualityControlDefect;
use App\Models\QualityControlDoc;
use App\Models\RmReception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RmReceptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RmReceptionsResource::collection(RmReception::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBoletaRMPRequest $request)
    {
        $data = $request->validated();
        $signature1 = $data['inspector_signature'];
        $signature2 = $data['prod_signature'];

        try {
            list(, $signature1) = explode(',', $signature1);
            list(, $signature2) = explode(',', $signature2);

            $signature1 = base64_decode($signature1);
            $signature2 = base64_decode($signature2);

            $filename1 = 'signatures/' . uniqid() . '.png';
            $filename2 = 'signatures/' . uniqid() . '.png';

            Storage::disk('public')->put($filename1, $signature1);
            Storage::disk('public')->put($filename2, $signature2);

            $product = Product::find($data['product_id']);
            $basket = Basket::find($data['basket_id']);

            $rm_reception = RmReception::create([
                'doc_date' => Carbon::now()
            ]);

            FieldDataReception::create([
                'coordinator_name' => $data['coordinator_name'],
                'rm_reception_id' => $rm_reception->id,
                'product_id' => $product->id,
                'transport' => $data['transport'],
                'pilot_name' => $data['pilot_name'],
                'inspector_name' => $data['inspector_name'],
                'cdp' => $data['cdp'],
                'transport_plate' => $data['transport_plate'],
                'weight' => $data['weight'],
                'total_baskets' => $data['total_baskets'],
                'weight_baskets' => round(($basket->weight * $data['total_baskets']), 2),
                'quality_percentage' => $data['quality_percentage'],
                'basket_id' => $basket->id,
                'inspector_signature' => $filename1,
                'prod_signature' => $filename2,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'message' => 'Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rm_reception = RmReception::find($id);

        return new RmReceptionDetailResource($rm_reception->load('field_data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProd(Request $request, string $id)
    {
        $data = $request->validate([
            'basket_id' => 'required',
            'receptor_signature' => 'required',
            'total_baskets' => 'required',
            'gross_weight' => 'required',
        ]);

        $signature1 = $data['receptor_signature'];

        try {
            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename1, $signature1);

            $basket = Basket::find($data['basket_id']);
            $rm_reception = RmReception::find($id);

            if (!$rm_reception) {
                return response()->json([
                    'message' => 'Doc Not Found'
                ], 404);
            }
            $tara =  $data['total_baskets'] * $basket->weight;

            ProdDataReception::create([
                'rm_reception_id' => $rm_reception->id,
                "total_baskets" => $data['total_baskets'],
                "weight_baskets" => $tara,
                "gross_weight" => $data['gross_weight'],
                "net_weight" => $data['gross_weight'] - $tara,
                "receptor_signature" => $filename1
            ]);

            $rm_reception->status = 2;
            $rm_reception->save();

            return response()->json([
                'message' => 'Data Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],500);
        }
    }

    public function updateCalidad(Request $request, string $id)
    {
        $data = $request->validate([
            'data' => 'required',
            'results' => 'required',
        ]);

        $rm_reception = RmReception::find($id);

        if (!$rm_reception) {
            return response()->json([
                'msg' => 'Doc Not Found'
            ], 404);
        }

        $signature1 = $data['data']['inspector_signature'];

        try {
            if($data['data']['isMinimunRequire']){
                $rm_reception->status = 3;
            }else{
                $rm_reception->status = 4;
            }
            $rm_reception->save();
            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';

            Storage::disk('public')->put($filename1, $signature1);
            $doc = QualityControlDoc::create([
                'rm_reception_id' => $rm_reception->id,
                'producer_id' => $data['data']['producer_id'],
                'net_weight' => $data['data']['net_weight'],
                'no_doc_cosechero' => $data['data']['no_doc_cosechero'],
                'sample_units' => $data['data']['sample_units'],
                'total_baskets' => $data['data']['total_baskets'],
                'ph' => $data['data']['ph'],
                'brix' => $data['data']['brix'],
                'percentage' => $data['data']['percentage'],
                'valid_pounds' => $data['data']['valid_pounds'],
                'user_id' => $request->user()->id,
                'doc_date' => Carbon::now(),
                'observations' => $data['data']['observations'],
                'inspector_signature' => $filename1
            ]);

            foreach ($data['results'] as $result) {
                $defect = Defect::find($result['id']);

                if(!$defect){
                    return response()->json([
                        'message' => 'Defect Not Found'
                    ], 404);
                }

                QualityControlDefect::create([
                    'quality_control_doc_id' => $doc->id,
                    'defect_id' => $defect->id,
                    'input' => $result['input'],
                    'result' => $result['result'],
                    'tolerance_percentage' => $result['tolerance_percentage']
                ]);
                
            }

            return response()->json([
                'message' => 'Data Updated Successfully'
            ]); 
        } catch (\Throwable $th) {
           return response()->json([
               'message' => $th->getMessage()
           ], 500);
        }
    }

    public function GenerateGRN(Request $request, string $id)
    {
        $data = $request->validate([
            'grn' => 'required',
        ]);

        $rm_reception = RmReception::find($id);

        if (!$rm_reception) {
            return response()->json([
                'msg' => 'Doc Not Found'
            ], 404);
        }

        try {
            $rm_reception->grn = $data['grn'];
            $rm_reception->status = 5;
            $rm_reception->save();

            return response()->json([
                'message' => 'Data Updated Successfully'
            ]);
        } catch (\Throwable $th) {
           return response()->json([
               'message' => $th->getMessage()
           ], 500);
        }

    }
}
