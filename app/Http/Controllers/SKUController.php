<?php

namespace App\Http\Controllers;

use App\Http\Resources\SKUResource;
use App\Http\Resources\SKUSelectResource;
use App\Models\StockKeepingUnit;
use Illuminate\Http\Request;

class SKUController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skus = StockKeepingUnit::select('id', 'name', 'code','unit_mesurment')->paginate(10);

        return SKUResource::collection($skus);

    }

    public function GetAllSKU()
    {
        $skus = StockKeepingUnit::select('id', 'name', 'code')->get();

        return SKUSelectResource::collection($skus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:stock_keeping_units,code',
            'name' => 'required',
            'unit_mesurment' => 'required'
        ]);

        try {
            StockKeepingUnit::create($data);

            return response()->json([
                'msg' => 'SKU Created Successfully'
            ],200);
        } catch (\Throwable $th) {
             return response()->json([
                'msg' => $th->getMessage()
            ],500);
        }
    }
}
