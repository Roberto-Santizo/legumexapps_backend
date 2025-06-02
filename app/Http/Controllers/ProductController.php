<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductByIdResource;
use App\Http\Resources\ProductResource;
use App\Models\Defect;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->query('paginated')){
            $products = Product::paginate(10);
        }else{
            $products = Product::all();
        }
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'data' => 'required',
            'defects' => 'required'
        ]);

        try {
            $product = Product::create($data['data']);

            foreach ($data['defects'] as $defect) {
                Defect::create([
                    'name' => $defect['name'],
                    'product_id' => $product->id,
                    'tolerance_percentage' => $defect['tolerance_percentage'],
                    'status' => 1
                ]);
            }

            return response()->json('Producto Creado Correctamente',200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'msg' => 'Not Found'
            ], 404);
        }

        return new ProductByIdResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'data' => 'required',
            'defects' => 'required|array'
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'msg' => 'Not Found'
            ], 404);
        }

        try {
            $product->update($data['data']);

            $existingDefectIds = $product->defects()->pluck('id')->toArray();

            $newDefectIds = collect($data['defects'])->pluck('id')->filter()->toArray();

            $product->defects()->whereNotIn('id', $newDefectIds)->delete();

            foreach ($data['defects'] as $defect) {
                if (isset($defect['id']) && in_array($defect['id'], $existingDefectIds)) {
                    Defect::where('id', $defect['id'])->update([
                        'name' => $defect['name'],
                        'tolerance_percentage' => $defect['tolerance_percentage'],
                        'status' => $defect['status']
                    ]);
                } 
                else {
                    Defect::create([
                        'name' => $defect['name'],
                        'product_id' => $product->id,
                        'tolerance_percentage' => $defect['tolerance_percentage'],
                        'status' => $defect['status']
                    ]);
                }
            }

            return response()->json([
                'msg' => 'Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
