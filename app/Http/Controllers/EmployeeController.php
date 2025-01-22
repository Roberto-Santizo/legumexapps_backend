<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Models\Employee;
use App\Models\TaskWeeklyPlan;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string',
        ]);

        $task = TaskWeeklyPlan::find($data['id']);
        $employees = Employee::where('terminal_id',$task->plan->finca->terminal_id)->get();

        return new EmployeeCollection($employees);
    }
}
