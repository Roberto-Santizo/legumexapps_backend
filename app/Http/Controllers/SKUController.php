<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockKeepingUnitRequest;
use App\Http\Resources\SKUResource;
use App\Models\StockKeepingUnit;
use App\Models\StockKeepingUnitRecipe;
use Illuminate\Http\Request;

class SKUController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $skus = StockKeepingUnit::paginate(10);
        } else {
            $skus = StockKeepingUnit::get();
        }
        return SKUResource::collection($skus);
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
}
