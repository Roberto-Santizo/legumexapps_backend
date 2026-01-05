<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFincaGroupRequest;
use App\Http\Resources\FincaGroupCollection;
use App\Http\Resources\FincaGroupResource;
use App\Models\FincaGroup;
use Illuminate\Http\Request;

class FincaGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = FincaGroup::query();

            if ($request->query('fincaId')) {
                $query->where('finca_id', $request->query('fincaId'));
            }

            $query->with(['employees' => function ($q) use ($request) {
                $q->where('weekly_plan_id', $request->query('plan'));
            }]);

            $query->with(['tasks' => function ($q) use ($request) {
                $q->where('weekly_plan_id', $request->query('plan'));
            }]);

            return new FincaGroupCollection($query->get());
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFincaGroupRequest $request)
    {
        $data = $request->validated();
        try {
            FincaGroup::create($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Grupo Creado Correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $group = FincaGroup::find($id);

            if (!$group) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'El grupo no existe'
                ], 404);
            }

            return response()->json([
                'statusCode' => 200,
                'data' => new FincaGroupResource($group)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }
}
