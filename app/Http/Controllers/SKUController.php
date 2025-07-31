<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockKeepingUnitRequest;
use App\Http\Resources\SKUResource;
use App\Http\Resources\StockKeepingUnitResource;
use App\Imports\RecipeStockKeepingUnitsImport;
use App\Imports\StockKeepingUnitsImport;
use App\Models\StockKeepingUnit;
use App\Models\StockKeepingUnitRecipe;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;

class SKUController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockKeepingUnit::query();

        $payload = JWTAuth::getPayload();
        $user = User::find($payload->get('id'));
        $role = User::find($payload->get('role'));

        $permissions = $user->getPermissionNames()->toArray();

        if ($request->query('product_name')) {
            $query->where('product_name', 'LIKE', '%' . $request->query('product_name') . '%');
        }

        if ($request->query('code')) {
            $query->where('code', 'LIKE', '%' . $request->query('code') . '%');
        }
        
        // if ($role != 'admin') {
        //     $query->where(function ($q) use ($permissions) {
        //         if (in_array('create pcs tasks', $permissions)) {
        //             $q->orWhere('code', 'LIKE', '%PCS%');
        //         }

        //         if (in_array('create pab tasks', $permissions)) {
        //             $q->orWhere('code', 'LIKE', '%PAB%');
        //         }

        //         if (in_array('create ptf tasks', $permissions)) {
        //             $q->orWhere('code', 'LIKE', '%PTF%');
        //         }
        //     });
        // }

        if ($request->query('paginated')) {
            return SKUResource::collection($query->paginate(10));
        } else {
            return SKUResource::collection($query->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStockKeepingUnitRequest $request)
    {
        $data = $request->validated();

        try {
            $sku = StockKeepingUnit::create([
                'code' => $data['code'],
                'product_name' => $data['product_name'],
                'presentation' => $data['presentation'] ?? null,
                'boxes_pallet' => $data['boxes_pallet'] ?? null,
                'pallets_container' => $data['pallets_container'] ?? null,
                'hours_container' => $data['hours_container'] ?? null,
                'client_name' => $data['client_name'] ?? null,
            ]);

            foreach ($data['recipe'] as $recipe) {
                StockKeepingUnitRecipe::create([
                    'sku_id' => $sku->id,
                    'item_id' => $recipe['packing_material_id'],
                    "lbs_per_item" => $recipe['lbs_per_item']
                ]);
            }

            return response()->json('SKU Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UploadStockKeepingUnits(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new StockKeepingUnitsImport, $request->file('file'));

            return response()->json("SKU's creados correctamente", 200);
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UploadSkuRecipe(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new RecipeStockKeepingUnitsImport, $request->file('file'));

            return response()->json("Recetas registradas correctamente", 200);
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $sku = StockKeepingUnit::where('id', $id)->with('items')->with('products')->first();

        if (!$sku) {
            return response()->json([
                'msg' => 'Sku No Encontrado'
            ], 404);
        }

        try {
            $data = new StockKeepingUnitResource($sku);

            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
