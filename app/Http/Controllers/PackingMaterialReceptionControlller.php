<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialReceptionRequest;
use App\Http\Resources\PackingMaterialReceiptResource;
use App\Models\PackingMaterialReceipt;
use App\Models\PackingMaterialReceiptDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class PackingMaterialReceptionControlller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PackingMaterialReceipt::query();

        if ($request->query('supervisor_name')) {
            $query->where('supervisor_name', 'like', '%' . $request->query('supervisor_name') . '%');
        }

        if ($request->query('received_by')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('received_by') . '%');
            });
        }

        if ($request->query('contains')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('p_material_id', $request->query('contains'));
            });
        }

        if ($request->query('receipt_date')) {
            $query->whereDate('receipt_date', $request->query('receipt_date'));
        }

        if ($request->query('invoice_date')) {
            $query->whereDate('invoice_date', $request->query('invoice_date'));
        }

        return PackingMaterialReceiptResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackingMaterialReceptionRequest $request)
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

            $receipt = PackingMaterialReceipt::create([
                'user_id' => $user_id,
                'supervisor_name' => $data['supervisor_name'],
                'invoice_date' => $data['invoice_date'],
                'receipt_date' => Carbon::now(),
                'observations' => $data['observations'] ?? null,
                'user_signature' => $filename2,
                'supervisor_signature' => $filename1
            ]);

            foreach ($data['items'] as $item) {
                PackingMaterialReceiptDetail::create([
                    'p_material_id' => $item['p_material_id'],
                    'pm_receipt_id' => $receipt->id,
                    'lote' => $item['lote'],
                    'quantity' => $item['quantity']
                ]);
            }

            return response()->json('Recibo Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $receipt = PackingMaterialReceipt::find($id);

        if (!$receipt) {
            return response()->json([
                'msg' => 'Recibo no Encontrado'
            ], 404);
        }

        return new PackingMaterialReceiptResource($receipt);
    }
}
