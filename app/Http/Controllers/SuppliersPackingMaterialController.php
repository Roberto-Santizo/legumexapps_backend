<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierPackingMaterialsResource;
use App\Models\SupplierPackingMaterial;
use Illuminate\Http\Request;

class SuppliersPackingMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierPackingMaterial::query();
        
        if($request->query('code')){
            $query->where('code','like','%'.$request->query('code').'%');
        }

        if($request->query('name')){
            $query->where('name','like','%'.$request->query('name').'%');
        }

        if($request->query('paginated')){
            return SupplierPackingMaterialsResource::collection($query->paginate(10));
        }else{
            return SupplierPackingMaterialsResource::collection($query->get());
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:supplier_packing_materials,code'
        ]);

        try {
            SupplierPackingMaterial::create($data);

            return response()->json('Proveedor Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error'
            ], 500);
        }
    }
}
