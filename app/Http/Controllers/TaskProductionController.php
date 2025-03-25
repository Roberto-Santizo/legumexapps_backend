<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeAssignmentRequest;
use App\Http\Resources\TaskProductionPlanDetailResource;
use App\Http\Resources\TaskProductionPlanDetailsResource;
use App\Http\Resources\TaskProductionPlanResource;
use App\Models\EmployeeTransfer;
use App\Models\Line;
use App\Models\StockKeepingUnit;
use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionEmployeesBitacora;
use App\Models\TaskProductionPerformance;
use App\Models\TaskProductionPlan;
use App\Models\TaskProductionTimeout;
use App\Models\Timeout;
use App\Models\WeeklyProductionPlan;
use App\Services\ChangeEmployeeNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskProductionController extends Controller
{
    protected $emailService;

    public function __construct(ChangeEmployeeNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks_production_plan = TaskProductionPlan::all();
        return TaskProductionPlanResource::collection($tasks_production_plan);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        return new TaskProductionPlanDetailsResource($task_production_plan);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tarimas' => 'required',
            'total_hours' => 'required',
            'operation_date' => 'required',
            'task_production_plan_id' => 'required',
        ]);

        $task_production = TaskProductionPlan::find($data['task_production_plan_id']);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        $weekly_plan = $task_production->weeklyPlan;
        $task = $weekly_plan->tasks()->where('line_id', $task_production->line_id)->OrderBy('priority')->get()->last();
        try {
            $task = TaskProductionPlan::create([
                'line_id' => $task_production->line_id,
                'weekly_production_plan_id' => $task_production->weekly_production_plan_id,
                'operation_date' => $data['operation_date'],
                'total_hours' => $data['total_hours'],
                'tarimas' => $data['tarimas'],
                'sku_id' => $task_production->sku_id,
                'status' => 1,
                'priority' => $task->priority + 1
            ]);

            foreach ($task_production->employees as $employee) {
                TaskProductionEmployee::create([
                    'task_p_id' => $task->id,
                    'name' => $employee->name,
                    'code' => $employee->code,
                    'position' => $employee->position,
                ]);
            }

            return response()->json([
                'msg' => 'Task Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
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
            'line_id' => 'required',
            'operation_date' => 'required',
            'total_hours' => 'required',
        ]);

        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        try {
            $task_production_plan->update($data);

            return response()->json([
                'msg' => 'Task Production Plan Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function AddTimeOut(Request $request, string $id)
    {
        $data = $request->validate([
            'timeout_id' => 'required'
        ]);

        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        $timeout = Timeout::find($data['timeout_id']);


        if (!$timeout) {
            return response()->json([
                'msg' => 'Timeout Not Found'
            ], 404);
        }

        try {
            TaskProductionTimeout::create([
                'timeout_id' => $data['timeout_id'],
                'task_p_id' => $task_production_plan->id,
            ]);

            return response()->json([
                'msg' => 'Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);;
        }
    }

    public function Assign(Request $request)
    {
        $data = $request->validate([
            'data' => 'required'
        ]);

        try {
            $groupedByLine = collect($data['data'])->groupBy('line');
            foreach ($groupedByLine as $key => $employees) {
                $line = Line::where('code', $key)->first();
                if (!$line) {
                    return response()->json([
                        'msg' => 'Line Not Found'
                    ], 404);
                }

                $task = $line->tasks->last();

                foreach ($employees as $employee) {
                    TaskProductionEmployee::create([
                        'task_p_id' => $task->id,
                        'name' => $employee['name'],
                        'code' => $employee['code'],
                        'position' => $employee['position'],
                    ]);
                }

                return response()->json([
                    'msg' => 'Created Successfully'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangeAssignment(ChangeAssignmentRequest $request)
    {
        $data = $request->validated();

        $assignment = TaskProductionEmployee::find($data['assignment_id']);

        if (!$assignment) {
            return response()->json([
                'msg' => 'Assignment Not Found'
            ], 400);
        }

        try {
            $change = TaskProductionEmployeesBitacora::create([
                "assignment_id" => $assignment->id,
                "original_name" => $assignment->name,
                "original_code" => $assignment->code,
                "original_position" => $assignment->position,
                "new_name" => $data['new_name'],
                "new_code" => $data['new_code'],
                "new_position" => $data['new_position']
            ]);

            $transfer = EmployeeTransfer::create([
                'change_employee_id' => $change->id,
                'confirmed' => false
            ]);

            $assignment->name = $data['new_name'];
            $assignment->code = $data['new_code'];
            $assignment->position = $data['new_position'];
            $assignment->save();


            $this->emailService->sendNotification($assignment, $change, $transfer);

            return response()->json([
                'msg' => 'Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 400);
        }
    }

    public function StartTaskProduction(string $id)
    {
        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production SKU Not Found'
            ], 404);
        }

        try {
            $task_production->start_date = Carbon::now();
            $task_production->save();

            return response()->json([
                'msg' => 'Task Production SKU Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function EndTaskProduction(Request $request, string $id)
    {
        $data = $request->validate([
            'total_tarimas' => 'required'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Not Found'
            ], 404);
        }

        try {
            $task_production->finished_tarimas = $data['total_tarimas'];
            $task_production->end_date = Carbon::now();
            $task_production->save();


            return response()->json([
                'msg' => 'Task Production Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function TaskDetails(string $id)
    {
        $task_production = TaskProductionPlan::find($id);

        return new TaskProductionPlanDetailResource($task_production);
    }

    public function TakePerformance(Request $request, string $id)
    {
        $data = $request->validate([
            'tarimas_produced' => 'required'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Not Found'
            ], 404);
        }

        try {
            TaskProductionPerformance::create([
                'task_production_plan_id' => $task_production->id,
                'tarimas_produced' => $data['tarimas_produced'],
            ]);

            return response()->json([
                'msg' => 'Data Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangePriority(Request $request)
    {
        $data = $request->validate([
            'data' => 'required'
        ]);

        try {
            foreach ($data['data'] as $key => $value) {
                $task_production = TaskProductionPlan::find($value);
                $task_production->priority = $key + 1;
                $task_production->save();
            }

            return response()->json([
                'msg' => 'Data Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangeOperationDate(Request $request, string $id)
    {
        $data = $request->validate([
            'date' => 'required'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        $new_date = Carbon::parse($data['date']);
        $old_date = $task_production->operation_date;
        $today = Carbon::today();
        
        if($new_date->weekOfYear != $today->weekOfYear){
            return response()->json([
                'msg' => 'No puede mover tareas fuera de la semana actual'
            ],500);
        }

        if(abs($today->diffInDays($new_date)) < 1){
            return response()->json([
                'msg' => 'No se pueden mover tareas entre fechas con una diferencia mayor a 1'
            ],500);
        }

        try {
            $last_task = TaskProductionPlan::whereDate('operation_date', $data['date'])->orderBy('priority', 'DESC')->get()->first();
         
            if ($last_task) {
                $task_production->priority = $last_task->priority + 1;
            } else {
                $task_production->priority = 1;
            }
            $task_production->operation_date = $data['date'];
            $task_production->save();

            $tasks_old_date = TaskProductionPlan::whereDate('operation_date', $old_date)->orderBy('priority', 'ASC')->get();
         
            if($tasks_old_date){
                foreach ($tasks_old_date as $key => $value) {
                    $value->priority = $key+1;
                    $value->save();
                }
            }

            return response()->json([
                'msg' =>  'Task Production Plan Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function CreateNewTaskProduction(Request $request)
    {
        $data = $request->validate([
            'line_id' => 'required',
            'operation_date' => 'required',
            'sku_id' => 'required',
            'tarimas' => 'required',
            'total_hours' => 'required'
        ]);


        $line = Line::find($data['line_id']);
        $sku = StockKeepingUnit::find($data['sku_id']);

        if (!$line) {
            return response()->json([
                'msg' => 'Line Not Found'
            ], 404);
        }

        if (!$sku) {
            return response()->json([
                'msg' => 'SKU Not Found'
            ], 404);
        }
        
        $operation_date = Carbon::parse($data['operation_date']);
        $today = Carbon::today();
        $limit_hour = Carbon::createFromTime(15, 0, 0); 
        $hour = Carbon::now();

        if(!$hour->lessThan($limit_hour)){
            return response()->json([
                'msg' => 'No se puede programar la tarea, la hora limite para poder programar son las 3:00 PM'
            ],500); 
        }
        
        if($operation_date->lessThan($today)){
            return response()->json([
                'msg' => 'No puede programar tareas a fechas pasadas'
            ],500); 
        }
        
        if(abs($today->diffInDays($operation_date)) < 1){
            return response()->json([
                'msg' => 'No puede programar tareas para el día de hoy'
            ],500);
        }

        try {
            $task_line = TaskProductionPlan::where('line_id', $line->id)->get()->last();
            $weekly_production_plan = WeeklyProductionPlan::all()->last();
            if (!$task_line) {
                return response()->json([
                    'msg' => 'Unvalid Data'
                ], 500);
            }

            $task_week = TaskProductionPlan::where('line_id', $line->id)->whereDate('operation_date', $data['operation_date'])->get()->last();
            $new_task = TaskProductionPlan::create([
                'line_id' => $line->id,
                'weekly_production_plan_id' => $weekly_production_plan->id,
                'operation_date' => $data['operation_date'],
                'total_hours' => $data['total_hours'],
                'sku_id' => $sku->id,
                'tarimas' => $data['tarimas'],
                'priority' => $task_week ? $task_week->priority + 1 : 1,
                'status' => 1
            ]);

            foreach ($task_line->employees as $employee) {
                TaskProductionEmployee::create([
                    'task_p_id' => $new_task->id,
                    'name' => $employee->name,
                    'code' => $employee->code,
                    'position' => $employee->position
                ]);
            }

            return response()->json([
                'msg' => 'Task Production Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
