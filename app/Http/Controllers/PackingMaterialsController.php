<?php

namespace App\Http\Controllers;

use App\Events\ChangeItemStatus;
use App\Http\Requests\CreatePackingMaterialRequest;
use App\Http\Resources\PackingMaterialResource;
use App\Imports\PackingMaterialImport;
use App\Models\PackingMaterial;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            $query->where('code', 'LIKE', '%' . $request->query('code') . '%');
        }

        if ($request->query('status')) {
            $status = ($request->query('status') === 'true') ? true : false;
            $query->where('blocked', $status);
        }

        if ($request->query('paginated')) {
            return PackingMaterialResource::collection($query->paginate(10));
        } else {
            return PackingMaterialResource::collection($query->get());
        }
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
    public function update(string $id)
    {
        $item = PackingMaterial::find($id);
        $payload = JWTAuth::getPayload();
        $user_id = $payload->get('id');


        if (!$item) {
            return response()->json([
                'msg' => 'El item no existe'
            ], 404);
        }

        try {
            $item->blocked = !$item->blocked;
            $item->save();

            broadcast(new ChangeItemStatus($user_id));
            return response()->json('Item actualizado correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UploadPackingMaterials(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new PackingMaterialImport, $request->file('file'));

            return response()->json("Items de Material de Empaque creados correctamente", 200);
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
