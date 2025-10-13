<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSeedingPlanRequest;
use App\Http\Resources\SeedingPlansCollection;
use App\Http\Resources\SeedingPlanResource;
use App\Imports\SeedingPlanImport;
use App\Models\DraftWeeklyPlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SeedingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit') ?? 10;
            return new SeedingPlansCollection(DraftWeeklyPlan::paginate($limit));
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'msg' => 'Hubo un error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSeedingPlanRequest $request)
    {
        $data = $request->validated();

        try {
            Excel::import(new SeedingPlanImport, $data['file']);

            return response()->json([
                "statusCode" => 201,
                "message" => "Plan de siembras creado correctamente"
            ], 201);
        } catch (HttpException  $th) {
            return response()->json([
                "statusCode" => $th->getStatusCode(),
                'msg' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $draft = DraftWeeklyPlan::find($id);
            $data = new SeedingPlanResource($draft);

            return response()->json([
                "statusCode" => 200,
                "data" => $data
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
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
