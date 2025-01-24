<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Models\Employee;
use App\Models\TaskWeeklyPlan;
use Carbon\Carbon;
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
        $employees = Employee::where('terminal_id',$task->plan->finca->terminal_id)->whereDate('punch_time',Carbon::now())->get();

        return new EmployeeCollection($employees);
    }
}
