<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBoletaRMPRequest;
use App\Http\Resources\RmReceptionDetailResource;
use App\Http\Resources\RmReceptionProdDataResource;
use App\Http\Resources\RmReceptionQualityDocDataResource;
use App\Http\Resources\RmReceptionsResource;
use App\Http\Resources\RmReceptionTransportDataResource;
use App\Models\Basket;
use App\Models\Defect;
use App\Models\FieldDataReception;
use App\Models\Finca;
use App\Models\ProdDataReception;
use App\Models\Producer;
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
    public function index(Request $request)
    {
        $query = RmReception::query();

        if ($request->has('quality_status_id')) {
            $query->where('quality_status_id', $request->quality_status_id);
        }

        if ($request->has('finca_id')) {
            $query->where('finca_id', $request->finca_id);
        }

        if ($request->has('ref_doc')) {
            $query->whereHas('field_data', function ($query) use ($request) {
                $query->where('ref_doc', $request->ref_doc);
            });
        }
        if ($request->has('grn')) {
            $query->where('grn', $request->grn);
        }

        if ($request->has('date')) {
            $query->whereDate('doc_date', $request->date);
        }

        if ($request->has('plate')) {
            $query->whereHas('field_data', function ($query) use ($request) {
                $query->whereHas('plate', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->plate}%");
                });
            });
        }


        if ($request->has('producer_id')) {
            $query->whereHas('field_data', function ($query) use ($request) {
                $query->where('producer_id', $request->producer_id);
            });
        }

        if ($request->has('product_id')) {
            $query->whereHas('field_data', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            });
        }

        if($request->query('paginated')){
            return RmReceptionsResource::collection($query->paginate(10));
        }else{
            return RmReceptionsResource::collection($query->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBoletaRMPRequest $request)
    {
        $data = $request->validated();
        $signature1 = $data['calidad_signature'];

        try {
            list(, $signature1) = explode(',', $signature1);

            $signature1 = base64_decode($signature1);

            $filename1 = 'signatures/' . uniqid() . '.png';

            Storage::disk('public')->put($filename1, $signature1);

            $product = Product::find($data['product_id']);
            $basket = Basket::find($data['basket_id']);
            $finca = Finca::find($data['finca_id']);
            $date = Carbon::parse($data['date']);
            $rm_reception = RmReception::create([
                'doc_date' => $date,
                'finca_id' => $finca->id,
                'consignacion' => 0,
                'quality_status_id' => 1
            ]);

            $producer = Producer::find($data['producer_id']);

            if (!$producer) {
                return response()->json([
                    'message' => 'Producer Not Found'
                ], 404);
            }

            FieldDataReception::create([
                'producer_id' => $producer->id,
                'rm_reception_id' => $rm_reception->id,
                'product_id' => $product->id,
                'inspector_name' => $data['inspector_name'],
                'weight' => $data['weight'],
                'total_baskets' => $data['total_baskets'],
                'weight_baskets' => round(($basket->weight * $data['total_baskets']), 2),
                'quality_percentage' => $data['quality_percentage'],
                'basket_id' => $basket->id,
                'calidad_signature' => $filename1,
                'plate_id' => $data['plate_id'],
                'cdp_id' => $data['productor_plantation_control_id'],
                'carrier_id' => $data['carrier_id'],
                'driver_id' => $data['driver_id'],
                'ref_doc' => $data['ref_doc']
            ]);

            return response()->json('Boleta Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear la boleta'
            ], 500);
        }
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

            $rm_reception->quality_status_id = 2;
            $rm_reception->save();

            return response()->json('Boleta de Recepción Creada Correctamente');
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear la boleta'
            ], 500);
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
            if (!$data['data']['isMinimunRequire']) {
                $rm_reception->consignacion = 1;
            }
            $rm_reception->quality_status_id = 3;
            $rm_reception->save();
            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';

            Storage::disk('public')->put($filename1, $signature1);
            $doc = QualityControlDoc::create([
                'rm_reception_id' => $rm_reception->id,
                'producer_id' => $rm_reception->field_data->producer->id,
                'net_weight' => $data['data']['net_weight'],
                'no_doc_cosechero' => $data['data']['no_doc_cosechero'] ?? null,
                'sample_units' => $data['data']['sample_units'],
                'total_baskets' => $data['data']['total_baskets'],
                'ph' => $data['data']['ph'] ?? null,
                'brix' => $data['data']['brix'] ?? null,
                'percentage' => $data['data']['percentage'],
                'valid_pounds' => $data['data']['valid_pounds'],
                'user_id' => $request->user()->id,
                'doc_date' => Carbon::now(),
                'observations' => $data['data']['observations'],
                'inspector_signature' => $filename1
            ]);

            foreach ($data['results'] as $result) {
                $defect = Defect::find($result['id']);

                if (!$defect) {
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

            return response()->json('Boleta Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear la boleta'
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
            $rm_reception->quality_status_id = 4;
            $rm_reception->save();

            return response()->json('GRN Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un Error Al Genenerar el GRN'
            ], 500);
        }
    }

    public function GetInfoDoc(string $id)
    {
        $rm_reception = RmReception::find($id);

        if (!$rm_reception) {
            return response()->json([
                'msg' => 'Doc Not Found'
            ], 404);
        }

        $field_data = new RmReceptionDetailResource($rm_reception->load('field_data'));
        $prod_data = $rm_reception->prod_data ? new RmReceptionProdDataResource($rm_reception->load('prod_data')) : null;
        $quality_doc_data = $rm_reception->quality_control_doc_data ? new RmReceptionQualityDocDataResource($rm_reception->load('quality_control_doc_data')) : null;
        $transport_data = $rm_reception->transport_doc_data ? new RmReceptionTransportDataResource($rm_reception->load('transport_doc_data')) : null;

        return response()->json([
            'status' => $rm_reception->quality_status_id,
            'finca' => $rm_reception->finca->name,
            'consignacion' => $rm_reception->consignacion ? true : false,
            'grn' => $rm_reception->grn,
            'field_data' => $field_data,
            'prod_data' => $prod_data,
            'quality_doc_data' => $quality_doc_data,
            'transport_data' => $transport_data
        ]);
    }

    public function RejectBoleta(string $id)
    {
        $rm_reception = RmReception::find($id);

        if (!$rm_reception) {
            return response()->json([
                'msg' => 'Rm Reception Not Foud'
            ], 404);
        }

        try {
            $rm_reception->quality_status_id = 5;
            $rm_reception->save();

            return response()->json('Boleta Rechazada', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al rechazar la boleta'
            ], 500);
        }
    }
}
