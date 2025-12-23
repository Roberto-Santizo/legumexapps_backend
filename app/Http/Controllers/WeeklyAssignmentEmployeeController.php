<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignFincaEmployeesGroupRequest;
use App\Http\Resources\WeeklyAssignmentEmployeeCollection;
use App\Http\Resources\WeeklyAssignmentEmployeeResource;
use App\Imports\WeeklyAssignmentEmployeesImport;
use App\Models\FincaGroup;
use App\Models\Lote;
use App\Models\WeeklyAssignmentEmployee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WeeklyAssignmentEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $id)
    {
        try {
            $query = WeeklyAssignmentEmployee::query();

            if ($request->query('name')) {
                $query->where('name', 'LIKE', '%' . $request->query('name') . '%');
            }

            if ($request->query('code')) {
                $query->where('code', 'LIKE', '%' . $request->query('code') . '%');
            }

            $assignments = $query->where('weekly_plan_id', $id)->where('finca_group_id', null)->get();

            return new WeeklyAssignmentEmployeeCollection($assignments);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        try {
            Excel::import(new WeeklyAssignmentEmployeesImport(), $request->file('file'));

            return response()->json([
                'statusCode' => 200,
                'message' => 'Empleados Cargados Correctamente'
            ], 200);
        } catch (HttpException $th) {
            return response()->json([
                "statusCode" => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $assignment = WeeklyAssignmentEmployee::find($id);

            if (!$assignment) {
                return response()->json([
                    "statusCode" => 404,
                    'msg' => 'Asignación no Encontrada'
                ], 404);
            }

            return response()->json([
                'statusCode' => 200,
                'response' => new WeeklyAssignmentEmployeeResource($assignment)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'lote_id' => 'required'
        ]);

        try {
            $assignment = WeeklyAssignmentEmployee::find($id);
            $lote = Lote::find($data['lote_id']);

            if (!$assignment) {
                return response()->json([
                    "statusCode" => 404,
                    'msg' => 'Asignación no Encontrada'
                ], 404);
            }

            if (!$lote) {
                return response()->json([
                    "statusCode" => 404,
                    'msg' => 'Lote no encontrado'
                ], 404);
            }

            $assignment->lote_id = $lote->id;
            $assignment->save();

            return response()->json([
                'statusCode' => 200,
                'msg' => 'Asignación actualizada correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $assignment = WeeklyAssignmentEmployee::find($id);

            if (!$assignment) {
                return response()->json([
                    "statusCode" => 404,
                    'msg' => 'Asignación no Encontrada'
                ], 404);
            }

            $assignment->delete();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Asignación eliminada correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function assignGroup(AssignFincaEmployeesGroupRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $group = FincaGroup::find($id);
            $assignments = WeeklyAssignmentEmployee::all();

            if (!$group) {
                return response()->json([
                    "statusCode" => 404,
                    'message' => 'Grupo no Encontrado'
                ], 404);
            }

            foreach ($data['data'] as $assignment) {
                $employee = $assignments->where('id', $assignment['assign_id'])->first();
                $employee->finca_group_id = $group->id;
                $employee->save();
            }

            return response()->json([
                "statusCode" => 200,
                'message' => 'Empleados Asignados Correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "statusCode" => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
