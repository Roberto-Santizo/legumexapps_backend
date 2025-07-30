<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRawMaterialItemRequest;
use App\Http\Resources\RawMaterialItemsResource;
use App\Models\RawMaterialItem;
use Illuminate\Http\Request;

class RawMaterialItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $query = RawMaterialItem::query();

            if ($request->query('code')) {
                $query->where('code', 'LIKE', $request->query('code'));
            }

            if ($request->query('paginated')) {
                return RawMaterialItemsResource::collection($query->paginate(10));
            } else {
                return RawMaterialItemsResource::collection($query->get());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRawMaterialItemRequest $request)
    {
        $data = $request->validated();

        try {
            RawMaterialItem::create($data);

            return response()->json('Item Creado Correctamente', 200);
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
        $item = RawMaterialItem::find($id);

        if (!$item) {
            return response()->json([
                'msg' => 'Item No Encontrado'
            ], 404);
        }

        try {
            $data =  new RawMaterialItemsResource($item);
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateRawMaterialItemRequest $request, string $id)
    {
        $data = $request->validated();

        $item = RawMaterialItem::find($id);

        if (!$item) {
            return response()->json([
                'msg' => 'Item No Encontrado'
            ], 404);
        }

        try {
            $item->update($data);

            return response()->json('Item Actualizado Correctamente');
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = RawMaterialItem::find($id);

        if (!$item) {
            return response()->json([
                'msg' => 'Item No Encontrado'
            ], 404);
        }

        try {
            $item->delete();

            return response()->json('Item Eliminado Correctamente');
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
