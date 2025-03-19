<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskCrop;
use App\Models\Finca;
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
        if($finca->id === 2){
            $employees = Employee::where(function ($query) {$query->where('terminal_id', 1008)->orWhere('terminal_id', 1009);})
            ->whereDate('punch_time', Carbon::now())
            ->get();
        }else{
            $employees = Employee::where('terminal_id',$finca->terminal_id)->whereDate('punch_time',Carbon::now())->get();
        }
        
        // $filter_employees = $employees->filter(function($employee){
        //     $assignment = EmployeeTask::where('employee_id',$employee->emp_id)->whereDate('created_at',Carbon::now())->whereHas('task_weekly_plan',function($query){
        //         $query->where('end_date',null);
        //     })->first();

        //     $assignmentCrop = EmployeeTaskCrop::where('employee_id',$employee->emp_id)->whereDate('created_at',Carbon::now())->whereHas('assignment',function($query){
        //         $query->where('end_date',null);
        //     })->first();

        //     if(!$assignment && !$assignmentCrop){
        //         return $employee;
        //     }
        // });

        return new EmployeeCollection($employees);
    }
}
