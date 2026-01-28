<?php

namespace App\Http\Controllers;

use App\Http\Resources\CropPartCollection;
use App\Models\CropPart;
use Illuminate\Http\Request;

class CropPartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = CropPart::query();

            if ($request->query('crop')) {
                $query->where('crop_id', $request->query('crop'));
            }

            if ($request->query('page')) {
                return new  CropPartCollection($query->paginate(10));
            } else {
                return new  CropPartCollection($query->get());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'crop_id' => ['required', 'exists:crops,id']
        ]);

        try {
            CropPart::create($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Parte Creada Correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
