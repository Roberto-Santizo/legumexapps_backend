<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsumosReceptionRequest;
use App\Http\Resources\InsumosReceiptDetailsResource;
use App\Http\Resources\InsumosReceptionPaginatedResource;
use App\Models\InsumosReceipt;
use App\Models\InsumosReceiptsDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        try {
            $receipt = InsumosReceipt::create([
                'user_id' => $request->user()->id,
                'supplier_id' => $data['supplier_id'],
                'supervisor_name' => $data['supervisor_name'],
                'invoice' => $data['invoice'],
                'received_date' => Carbon::now(),
                'invoice_date' => $data['invoice_date'],
                'user_signature' => $data['user_signature'],
                'supervisor_signature' => $data['supervisor_signature']
            ]);

            foreach ($data['items'] as $item) {
                InsumosReceiptsDetail::create([
                    'insumo_id' => $item['insumo_id'],
                    'insumos_receipt_id' => $receipt->id,
                    'units' => $item['units'],
                    'total' => $item['total']
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
