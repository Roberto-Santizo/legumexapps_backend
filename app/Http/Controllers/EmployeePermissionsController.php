<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeTransferResource;
use App\Models\EmployeeTransfer;
use App\Models\TaskProductionEmployee;
use Illuminate\Http\Request;

class EmployeePermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee_permissions = EmployeeTransfer::OrderBy('created_at', 'DESC')->paginate(10);
        return EmployeeTransferResource::collection($employee_permissions);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $change = EmployeeTransfer::find($id);
        return new EmployeeTransferResource($change);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'permission' => 'required|boolean',
        ]);

        $change = EmployeeTransfer::find($id);

        if (!$change) {
            return response()->json(['msg' => 'Employee Transfer not found'], 404);
        }

        try {
            $change->permission = $data['permission'];
            $change->confirmed = true;
            $change->save();

            if ($data['permission'] == '0') {
                $line = $change->bitacora->assignment->TaskProduction->line->code;
                $employees = TaskProductionEmployee::whereHas('TaskProduction', function ($query) use ($line) {
                    $query->whereHas('line', function ($query) use ($line) {
                        $query->where('code', $line);
                    });
                })->where('position', $change->bitacora->original_position)->get();

                foreach ($employees as $employee) {
                    $employee->name = $change->bitacora->new_name;
                    $employee->code = $change->bitacora->new_code;
                    $employee->position = $change->bitacora->new_position;
                    $employee->save();
                }
            }
            return response()->json(['msg' => 'Employee Transfer updated successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ]);
        }
    }
}
