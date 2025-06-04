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

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'performance' => 'sometimes',
            'accepted_percentage' => 'required',
            'payment_method' => 'required'
        ]);

        $line_sku = LineStockKeepingUnits::find($id);

        if (!$line_sku) {
            return response()->json([
                'errors' => 'El SKU no existe'
            ], 404);
        }

        try {
            $line_sku->lbs_performance = $data['performance'];
            $line_sku->accepted_percentage = $data['accepted_percentage'];
            $line_sku->payment_method = $data['payment_method'];

            $line_sku->save();

            return response()->json('SKU Actualizado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al actualizar el SKU'
            ], 500);
        }
    }
}
