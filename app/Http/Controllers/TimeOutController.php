<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeoutResource;
use App\Http\Resources\TimeoutSelectResource;
use App\Models\Timeout;
use Illuminate\Http\Request;

class TimeOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeouts = Timeout::paginate(10);
        return TimeoutResource::collection($timeouts);
    }

    public function GetAllTimeouts()
    {
        $timeouts = Timeout::all();
        return TimeoutSelectResource::collection($timeouts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        try {
            Timeout::create([
                'name' => $data['name'],
            ]);

            return response()->json('Tiempo Muerto Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $timeout = Timeout::select(['id','name'])->find($id);

        if (!$timeout) {
            return response()->json([
                'msg' => 'Timeout Not Found'
            ], 404);
        }

        return new TimeoutResource($timeout);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        $timeout = Timeout::find($id);

        if (!$timeout) {
            return response()->json([
                'msg' => 'Timeout not found'
            ], 404);
        }

        try {
            $timeout->update($data);
            $timeout->save();

            return response()->json('Cambios Guardados Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
