<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLineStockKeepingUnitRequest;
use App\Http\Resources\LineStockKeepingUnitsResource;
use App\Imports\LineStockKeepingUnitsImport;
use App\Models\LineStockKeepingUnits;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LineStockKeepingUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LinestockKeepingUnits::query();

        if ($request->query('line')) {
            $query->whereHas('line', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->query('line') . '%');
            });
        }

        if ($request->query('sku')) {
            $query->whereHas('sku', function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->query('sku') . '%');
            });
        }

        $lines_skus = $query->paginate(10);

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
            'payment_method' => 'required',
            'status' => 'required'
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
            $line_sku->status = $data['status'];
            $line_sku->save();

            return response()->json('SKU Actualizado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al actualizar el SKU'
            ], 500);
        }
    }

    public function UploadLinesSkus(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);


        try {
            Excel::import(new LineStockKeepingUnitsImport, $request->file('file'));

            return response()->json("Lineas creados correctamente", 200);
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
