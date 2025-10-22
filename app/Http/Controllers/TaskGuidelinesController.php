<?php

namespace App\Http\Controllers;

use App\Exports\TaskGuidelinesExport;
use App\Http\Requests\CreateTaskGuidelineRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UploadTasksGuidelinesRequest;
use App\Http\Resources\TaskGuidelineCollection;
use App\Imports\TasksGuidelinesImport;
use App\Models\TaskGuideline;
use App\Models\TaskInsumoRecipe;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskGuidelinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaskGuideline::query();

        try {
            if ($request->query('week')) {
                $query->where('week', $request->query('week'));
            }

            if ($request->query('recipe')) {
                $query->whereHas('recipe', function ($q) use ($request) {
                    $q->where('name',  'LIKE', '%' . $request->query('recipe') . '%');
                });
            }

            if ($request->query('crop')) {
                $query->whereHas('crop', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->query('crop') . '%');
                });
            }

            $limit = $request->query('limit');

            if ($limit) {
                return new TaskGuidelineCollection($query->paginate($limit));
            } else {
                return new TaskGuidelineCollection($query->get());
            }
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
    public function store(CreateTaskGuidelineRequest $request)
    {
        $data = $request->validated();

        try {
            $task = TaskGuideline::create($data);

            foreach ($data['insumos'] as $insumo) {
                TaskInsumoRecipe::create([
                    'insumo_id' => $insumo['insumo_id'],
                    'quantity' => $insumo['quantity'],
                    'task_guideline_id' => $task->id
                ]);
            }

            return response()->json([
                'statusCode' => 201,
                'message' => 'Guía de tarea creada correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateTaskGuidelineRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            $task_guideline = TaskGuideline::find($id);

            if (!$task_guideline) {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Tarea no Encontrada"
                ], 404);
            }

            $task_guideline->update($data);

            return response()->json([
                'statusCode' => 201,
                'message' => 'Guía de tarea actualizada correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Hubo un error'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $file = Excel::raw(new TaskGuidelinesExport(), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = "Manual de Tareas.xlsx";

            return response()->json([
                'fileName' => $fileName,
                'file' => base64_encode($file)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function upload(UploadTasksGuidelinesRequest $request)
    {
        $data = $request->validated();

        try {
            Excel::import(new TasksGuidelinesImport, $data['file']);

            return response()->json([
                "statusCode" => 201,
                "message" => "Tareas Creadas Correctamente"
            ], 201);
        } catch (HttpException $th) {
            return response()->json([
                "statusCode" => $th->getStatusCode(),
                'msg' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    public function uploadInsumosRecipe(UploadFileRequest $request)
    {
        $data = $request->validated();
        try {
            dd($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
