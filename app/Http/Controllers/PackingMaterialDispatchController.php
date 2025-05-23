<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialDispatchResource;
use App\Http\Resources\PackingMaterialDispatchDetailsResource;
use App\Http\Resources\PackingMaterialDispatchResource;
use App\Http\Resources\PackingMaterialResource;
use App\Models\PackingMaterialDispatch;
use App\Models\PackingMaterialDispatchDetails;
use App\Models\TaskProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackingMaterialDispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PackingMaterialDispatch::query();

        if ($request->query('paginated')) {
            return PackingMaterialDispatchResource::collection($query->paginate(10));
        }

        return PackingMaterialDispatchResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackingMaterialDispatchResource $request)
    {
        $data = $request->validated();

        try {
            $task = TaskProductionPlan::find($data['task_production_plan_id'] ?? null);

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


            $dispatch = PackingMaterialDispatch::create([
                'task_production_plan_id' => $data['task_production_plan_id'] ?? null,
                'user_id' => $request->user()->id,
                'reference' => $data['reference'],
                'responsable' => $data['responsable'],
                'responsable_signature' => $filename1,
                'user_signature' => $filename2,
                'observations' => $data['observations'],
            ]);

            foreach ($data['items'] as $item) {
                PackingMaterialDispatchDetails::create([
                    'pm_dispatch_id' => $dispatch->id,
                    'packing_material_id' => $item['packing_material_id'],
                    'quantity' => $item['quantity'],
                    'lote' => $item['lote'],
                    'destination' => $item['destination'] ?? null
                ]);
            }

            if ($task) {
                $task->status = 1;
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
        $dispatch = PackingMaterialDispatch::find($id);

        if (!$dispatch) {
            return response()->json([
                'msg' => 'Boleta de Salida no Encontrada'
            ], 404);
        }

        return new PackingMaterialDispatchDetailsResource($dispatch);
    }
}
