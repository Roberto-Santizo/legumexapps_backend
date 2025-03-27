<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockKeepingUnitsProductsResource;
use App\Http\Resources\StockKeepingUnitsProductsSelectResource;
use App\Models\StockKeepingUnitsProduct;
use Illuminate\Http\Request;

class ProductsSKUController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = StockKeepingUnitsProduct::select('name','presentation','box_weight')->paginate(10);
        return StockKeepingUnitsProductsResource::collection($products);
    }

    public function GetAllProducts() 
    {
        $products = StockKeepingUnitsProduct::all();
        return StockKeepingUnitsProductsSelectResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'presentation' => 'required',
            'box_weight' => 'sometimes'
        ]);

        try {
            StockKeepingUnitsProduct::create($data);

            return response()->json('Producto Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
