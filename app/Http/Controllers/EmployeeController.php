<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\Finca;
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

        $finca = Finca::find($data['id']);
        $employees = Employee::where('terminal_id',$finca->terminal_id)->whereDate('punch_time',Carbon::now())->get();
        
        $filter_employees = $employees->filter(function($employee){
            $assignment = EmployeeTask::where('employee_id',$employee->emp_id)->whereDate('created_at',Carbon::now())->first();
            if(!$assignment){
                return $employee;
            }
        });

        return new EmployeeCollection($filter_employees);
    }
}
