<?php

namespace App\Http\Controllers;

use App\Http\Resources\VarietyProductResource;
use App\Models\VarietyProduct;
use Illuminate\Http\Request;

class VarietyProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $varieties = VarietyProduct::paginate(10);
        } else {
            $varieties = VarietyProduct::get();
        }
        return VarietyProductResource::collection($varieties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        try {
            VarietyProduct::create([
                'name' => $data['name']
            ]);

            return response()->json([
                'msg' => 'Created Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
