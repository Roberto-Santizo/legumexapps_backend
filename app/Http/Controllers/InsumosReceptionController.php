<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsumosReceptionRequest;
use App\Http\Resources\InsumosReceiptDetailsResource;
use App\Http\Resources\InsumosReceptionPaginatedResource;
use App\Models\Insumo;
use App\Models\InsumosReceipt;
use App\Models\InsumosReceiptsDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class InsumosReceptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $receipts = InsumosReceipt::query();

        if ($request->query('invoice')) {
            $receipts->where('invoice', 'like', '%' . $request->query('invoice') . '%');
        }

        if ($request->query('received_by')) {
            $receipts->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('received_by') . '%');
            });
        }

        if ($request->query('received_date')) {
            $receipts->whereDate('received_date', $request->query('received_date'));
        }

        if ($request->query('invoice_date')) {
            $receipts->whereDate('invoice_date', $request->query('invoice_date'));
        }

        return InsumosReceptionPaginatedResource::collection($receipts->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInsumosReceptionRequest $request)
    {
        $data = $request->validated();
        $signature1 = $data['supervisor_signature'];
        $signature2 = $data['user_signature'];

        try {
            $payload = JWTAuth::getPayload();
            $user_id = $payload->get('id');


            //SUPERVISOR
            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename1, $signature1);

            //RECEPTOR
            list(, $signature2) = explode(',', $signature2);
            $signature2 = base64_decode($signature2);
            $filename2 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename2, $signature2);


            $receipt = InsumosReceipt::create([
                'user_id' => $user_id,
                'supplier_id' => $data['supplier_id'],
                'supervisor_name' => $data['supervisor_name'],
                'invoice' => $data['invoice'],
                'received_date' => Carbon::now(),
                'invoice_date' => $data['invoice_date'],
                'user_signature' => $filename2,
                'supervisor_signature' => $filename1
            ]);

            foreach ($data['items'] as $item) {
                $insumo = Insumo::find($item['insumo_id']);
                if (!$insumo) {
                    return response()->json([
                        'errors' => 'El insumo no existe'
                    ], 404);
                }
                InsumosReceiptsDetail::create([
                    'insumo_id' => $item['insumo_id'],
                    'insumos_receipt_id' => $receipt->id,
                    'units' => $item['units'],
                    'total' => $insumo->unit_value * $item['units']
                ]);
            }

            return response()->json('Recibo Creado Correctamente', 200);
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
        $receipt = InsumosReceipt::find($id);

        if (!$receipt) {
            return response()->json([
                'msg' => 'Recibo no Encontrado'
            ], 404);
        }

        return new InsumosReceiptDetailsResource($receipt);
    }
}
