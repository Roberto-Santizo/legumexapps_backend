<?php

namespace App\Http\Controllers;

use App\Http\Resources\BiometricEmployeeResource;
use App\Http\Resources\EmployeeCollection;
use App\Models\BiometricEmployee;
use App\Models\BiometricTransaction;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskCrop;
use App\Models\Finca;
use App\Models\TaskProductionEmployee;
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
        if ($finca->id === 2) {
            $employees = Employee::where(function ($query) {
                $query->where('terminal_id', 1008)->orWhere('terminal_id', 1009);
            })
                ->whereDate('punch_time', Carbon::now())
                ->get();
        } else {
            $employees = Employee::where('terminal_id', $finca->terminal_id)->whereDate('punch_time', Carbon::now())->get();
        }

        $filter_employees = $employees->filter(function ($employee) {
            $assignment = EmployeeTask::where('employee_id', $employee->emp_id)->whereDate('created_at', Carbon::now())->whereHas('task_weekly_plan', function ($query) {
                $query->where('end_date', null);
            })->first();

            $assignmentCrop = EmployeeTaskCrop::where('employee_id', $employee->emp_id)->whereDate('created_at', Carbon::now())->whereHas('assignment', function ($query) {
                $query->where('end_date', null);
            })->first();

            if (!$assignment && !$assignmentCrop) {
                return $employee;
            }
        });

        return new EmployeeCollection($filter_employees);
    }

   public function getComodines()
    {
        $today = Carbon::today();

        $comodines = BiometricEmployee::where('last_name', 'LIKE', '%LDC%')->get();

        $entrances = BiometricTransaction::whereDate('event_time', $today)
            ->whereIn('pin', $comodines->pluck('pin'))
            ->get()
            ->groupBy('pin');

        $assigned = TaskProductionEmployee::whereIn('position', $comodines->pluck('last_name'))
            ->whereHas('TaskProduction', function ($query) use ($today) {
                $query->whereDate('operation_date', $today);
            })
            ->get()
            ->groupBy('position');

        $comodinesFiltrados = $comodines->filter(function ($comodin, $index) use ($entrances, $assigned) {
            $comodin->temp_id = $index + 10;

            $hasEntrance = $entrances->has($comodin->pin);
            $isAssigned = $assigned->has($comodin->last_name);

            return $hasEntrance && !$isAssigned;
        });

        return BiometricEmployeeResource::collection($comodinesFiltrados);
    }

}
