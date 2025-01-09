<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTareaRequest;
use App\Http\Requests\UpdateTareaRequest;
use App\Http\Resources\TareaCollection;
use App\Http\Resources\TareaResource;
use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new TareaCollection(Tarea::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTareaRequest $request)
    {
        $data = $request->validated();

        $tarea = Tarea::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'],
        ]);

        return response()->json([
            'data' => $tarea
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarea $tarea)
    {   

        if(!$tarea){
            return response()->json([
                'message' => 'Tarea no encontrada :('
            ],404);
        }
        return new TareaResource($tarea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTareaRequest $request, Tarea $tarea)
    {
        $data = $request->validated();
        $tarea->update($data);
        
        return response()->json([
            'data' => $tarea
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
