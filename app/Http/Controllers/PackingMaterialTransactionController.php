<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialDispatchResource;
use App\Http\Resources\PackingMaterialTransactionDetailsResource;
use App\Http\Resources\PackingMaterialTransactionResource;
use App\Models\PackingMaterialTransaction;
use App\Models\PackingMaterialTransactionDetail;
use App\Models\PackingMaterialWastage;
use App\Models\TaskProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class PackingMaterialTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PackingMaterialTransaction::query();

        if ($request->query('transaction')) {
            $query->where('reference', $request->query('transaction'));
        }

        if ($request->query('responsable')) {
            $query->where('responsable', 'LIKE', '%' . $request->query('responsable') . '%');
        }

        if ($request->query('delivered_by')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->query('delivered_by') . '%');
            });
        }

        if ($request->query('delivered_date')) {
            $query->whereDate('created_at', $request->query('delivered_date'));
        }

        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->query('paginated')) {
            return PackingMaterialTransactionResource::collection($query->paginate(10));
        }

        return PackingMaterialTransactionResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackingMaterialDispatchResource $request)
    {
        $data = $request->validated();

        try {
            $task = TaskProductionPlan::find($data['task_production_plan_id'] ?? null);

            $payload = JWTAuth::getPayload();
            $user_id = $payload->get('id');


            $signature1 = $data['responsable_signature'];
            $signature2 = $data['user_signature'];

            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename1, $signature1);

            list(, $signature2) = explode(',', $signature2);
            $signature2 = base64_decode($signature2);
            $filename2 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename2, $signature2);


            $dispatch = PackingMaterialTransaction::create([
                'task_production_plan_id' => $data['task_production_plan_id'] ?? null,
                'user_id' => $user_id,
                'reference' => $data['reference'],
                'responsable' => $data['responsable'],
                'responsable_signature' => $filename1,
                'user_signature' => $filename2,
                'observations' => $data['observations'],
                'type' => $data['type'],
            ]);

            foreach ($data['items'] as $item) {
                PackingMaterialTransactionDetail::create([
                    'pm_transaction_id' => $dispatch->id,
                    'packing_material_id' => $item['packing_material_id'],
                    'quantity' => $item['quantity'],
                    'lote' => $item['lote'],
                    'destination' => $item['destination'] ?? null
                ]);
            }

            foreach ($data['wastages'] as $wastage) {
                PackingMaterialWastage::create([
                    'task_p_id' => $task->id,
                    'packing_material_id' => $wastage['packing_material_id'],
                    'quantity' => $wastage['quantity'],
                    'lote' => $wastage['lote']
                ]);
            }

            if ($task && !($task->status > 3)) {
                $task->status = 3;
                $task->save();
            }

            return response()->json('Registro Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dispatch = PackingMaterialTransaction::find($id);

        if (!$dispatch) {
            return response()->json([
                'msg' => 'Boleta de Salida no Encontrada'
            ], 404);
        }

        $data = new PackingMaterialTransactionDetailsResource($dispatch);
        return response()->json($data);
    }
}
