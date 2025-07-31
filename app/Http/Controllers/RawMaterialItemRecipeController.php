<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRawMaterialSkuRecipeRequest;
use App\Http\Resources\StockKeepingUnitRecipeRawMaterialResource;
use App\Imports\StockKeepingUnitsRecipeRawMaterialImport;
use App\Models\RawMaterialSkuRecipe;
use App\Models\StockKeepingUnit;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RawMaterialItemRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $stock_keeping_unit_id)
    {
        $sku = StockKeepingUnit::find($stock_keeping_unit_id);

        if (!$sku) {
            return response()->json([
                'msg' => 'SKU no encontrado'
            ], 404);
        }

        try {
            $items = RawMaterialSkuRecipe::where('stock_keeping_unit_id', $sku->id)->get();

            $data = StockKeepingUnitRecipeRawMaterialResource::collection($items);

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $recipe = RawMaterialSkuRecipe::find($id);

        if (!$recipe) {
            return response()->json([
                'msg' => 'Item no encontrado'
            ], 404);
        }

        try {
            return response()->json($recipe);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRawMaterialSkuRecipeRequest $request, string $stock_keeping_unit_id)
    {
        $data = $request->validated();
        $sku = StockKeepingUnit::find($stock_keeping_unit_id);

        if (!$sku) {
            return response()->json([
                'msg' => 'SKU no encontrado'
            ], 404);
        }

        try {
            RawMaterialSkuRecipe::create([
                'stock_keeping_unit_id' => $sku->id,
                'raw_material_item_id' => $data['raw_material_item_id'],
                'percentage' => $data['percentage']
            ]);

            return response()->json('Item Creado Correctamente');
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateRawMaterialSkuRecipeRequest $request, string $id)
    {
        $data = $request->validated();
        $recipe = RawMaterialSkuRecipe::find($id);

        if (!$recipe) {
            return response()->json([
                'msg' => 'El item de receta no encontrado'
            ], 404);
        }

        try {
            $recipe->update($data);

            return response()->json('Item Actualizado Correctamente');
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {

            Excel::import(new StockKeepingUnitsRecipeRawMaterialImport, $request->file('file'));

            return response()->json("Recetas creadas correctamente", 200);
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
