<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLineStockKeepingUnitRequest;
use App\Http\Resources\LineStockKeepingUnitsResource;
use App\Models\LineStockKeepingUnits;
use Illuminate\Http\Request;

class LineStockKeepingUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lines_skus = LineStockKeepingUnits::paginate(10);

        return LineStockKeepingUnitsResource::collection($lines_skus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLineStockKeepingUnitRequest $request)
    {
        $data = $request->validated();

        try {
            LineStockKeepingUnits::create($data);
            return response()->json('SKU Relacionado a una Linea Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
