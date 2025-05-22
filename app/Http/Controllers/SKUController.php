<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockKeepingUnitRequest;
use App\Http\Resources\SKUResource;
use App\Models\StockKeepingUnit;
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
            StockKeepingUnit::create($data);

            return response()->json('SKU Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
