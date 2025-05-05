<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackingMaterialRequest;
use App\Http\Resources\PackingMaterialResource;
use App\Models\PackingMaterial;
use Illuminate\Http\Request;

class PackingMaterialsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PackingMaterial::query();

        if ($request->query('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->query('code')) {
            $query->where('code', $request->query('code'));
        }

        if ($request->query('status')) {
            $status = ($request->query('status') === 'true') ? true : false;
            $query->where('blocked', $status);
        }

        $data = $query->paginate(10);

        return PackingMaterialResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackingMaterialRequest $request)
    {
        $data = $request->validated();

        try {
            PackingMaterial::create($data);

            return response()->json('Item creado correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = PackingMaterial::find($id);

        if(!$item){
            return response()->json([
                'msg' => 'El item no existe' 
            ],404);
        }

        try {
            $item->blocked = !$item->blocked;
            $item->save();
            
            return response()->json('Item actualizado correctamente',200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ],500);
        }
    }
}
