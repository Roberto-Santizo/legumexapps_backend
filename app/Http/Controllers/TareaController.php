<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTareaRequest;
use App\Http\Requests\UpdateTareaRequest;
use App\Http\Resources\TareaCollection;
use App\Http\Resources\TareaResource;
use App\Imports\TasksImport;
use App\Models\Tarea;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tarea::query();

        if ($request->query('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->query('code')) {
            $query->where('code', $request->query('code'));
        }

        if ($request->query('paginated')) {
            return new TareaCollection($query->paginate(10));
        } else {
            return new TareaCollection($query->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTareaRequest $request)
    {
        $data = $request->validated();

        try {
            Tarea::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'] ?? '',
            ]);

            return response()->json('Tarea Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear la tarea'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tarea = Tarea::find($id);

        if (!$tarea) {
            return response()->json([
                'message' => 'Tarea no encontrada'
            ], 404);
        }
        return new TareaResource($tarea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTareaRequest $request, string $id)
    {
        $tarea = Tarea::find($id);
        $data = $request->validated();

        if (!$tarea) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            $tarea->update($data);

            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear la tarea'
            ], 500);
        }
    }

    public function UploadTasks(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new TasksImport, $request->file('file'));
            return response()->json('Tareas Creadas Correctamente', 200);
        } catch (Exception $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
