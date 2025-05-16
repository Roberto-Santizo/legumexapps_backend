<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialDispatchResource;
use App\Http\Resources\PackingMaterialDispatchDetailsResource;
use App\Http\Resources\PackingMaterialDispatchResource;
use App\Http\Resources\PackingMaterialResource;
use App\Models\PackingMaterialDispatch;
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
            $signature1 = $data['user_signature'];
            $signature2 = $data['received_by_signature_boxes'];
            $signature3 = $data['received_by_signature_bags'];

            //USUARIO
            list(, $signature1) = explode(',', $signature1);
            $signature1 = base64_decode($signature1);
            $filename1 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename1, $signature1);

            //RECEPTOR CAJAS
            list(, $signature2) = explode(',', $signature2);
            $signature2 = base64_decode($signature2);
            $filename2 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename2, $signature2);

            //RECEPTOR BOLSAS
            list(, $signature3) = explode(',', $signature3);
            $signature3 = base64_decode($signature3);
            $filename3 = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($filename2, $signature3);


            $data['received_by_signature_boxes'] = $filename1;
            $data['received_by_signature_bags'] = $filename2;
            $data['user_signature'] = $filename3;
            $data['user_id'] = $request->user()->id;

            $task = TaskProductionPlan::find($data['task_production_plan_id']);

            PackingMaterialDispatch::create($data);


            $task->status = 1;
            $task->save();
            return response()->json('Despacho Creado Correctamente', 200);
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
        $dispatch = PackingMaterialDispatch::find($id);

        if (!$dispatch) {
            return response()->json([
                'msg' => 'Boleta de Salida no Encontrada'
            ], 404);
        }

        return new PackingMaterialDispatchDetailsResource($dispatch);
    }
}
