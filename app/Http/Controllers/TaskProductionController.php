<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeAssigmentsRequest;
use App\Http\Requests\CreateAssigmentsRequest;
use App\Http\Requests\CreateTaskProductionRequest;
use App\Http\Resources\FinishedTaskProductionResource;
use App\Http\Resources\TaskPackingMaterialReturnDetailsResource;
use App\Http\Resources\TaskProductionEditDetailsResource;
use App\Http\Resources\TaskProductionEmployeeResource;
use App\Http\Resources\TaskProductionPlanDetailResource;
use App\Http\Resources\TaskProductionPlanDetailsResource;
use App\Http\Resources\TaskProductionPlanResource;
use App\Models\EmployeeTransfer;
use App\Models\Line;
use App\Models\LinePosition;
use App\Models\LineStockKeepingUnits;
use App\Models\ProductionOperationChange;
use App\Models\StockKeepingUnit;
use App\Models\TaskOperationDateBitacora;
use App\Models\TaskProductionEmployee;
use App\Models\TaskProductionEmployeesBitacora;
use App\Models\TaskProductionPerformance;
use App\Models\TaskProductionPlan;
use App\Models\TaskProductionTimeout;
use App\Models\TaskProductionUnassign;
use App\Models\TaskProductionUnassignAssignment;
use App\Models\Timeout;
use App\Models\WeeklyProductionPlan;
use App\Services\AssignEmployeeNotificationService;
use App\Services\ChangeEmployeeNotificationService;
use App\Services\ReturnPackingMaterialNotificationService;
use Carbon\Carbon;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskProductionController extends Controller
{
    protected $emailService;
    protected $emailCreateAssigneeService;
    protected $emailReturnPackingMaterialService;

    public function __construct(ChangeEmployeeNotificationService $emailService, AssignEmployeeNotificationService $emailCreateAssigneeService, ReturnPackingMaterialNotificationService $emailReturnPackingMaterialService)
    {
        $this->emailService = $emailService;
        $this->emailCreateAssigneeService = $emailCreateAssigneeService;
        $this->emailReturnPackingMaterialService = $emailReturnPackingMaterialService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks_production_plan = TaskProductionPlan::whereNull('end_date')->get();
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
            'sku_id' => 'required',
            'line_id' => 'required',
            'total_lbs' => 'required',
            'destination' => 'required',
            'operation_date' => 'sometimes'
        ]);

        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            $line_sku = LineStockKeepingUnits::where('line_id', $data['line_id'])->where('sku_id', $data['sku_id'])->first();
            if (!$line_sku) {
                return response()->json([
                    'msg' => 'La linea no cuenta con relación con el sku seleccionado'
                ], 404);
            }

            $data['line_sku'] = $line_sku->id;
            $data['line_id'] = $line_sku->line_id;

            $task_production_plan->update($data);

            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function AddTimeOutOpen(Request $request, string $id)
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
                'start_date' => Carbon::now()
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

    public function AddTimeOutClose(string $id)
    {
        $task_production_plan = TaskProductionPlan::find($id);

        if (!$task_production_plan) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        try {
            $timeout = $task_production_plan->timeouts->last();
            $timeout->end_date = Carbon::now();
            $timeout->save();
            return response()->json('Tarea Reanudada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
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
            $task_production->status = 4;
            $task_production->save();

            return response()->json('Tarea Iniciada', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function EndTaskProduction(Request $request, string $id)
    {
        $data = $request->validate([
            'total_tarimas' => 'sometimes',
            'total_lbs_bascula' => 'required'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Not Found'
            ], 404);
        }

        try {
            $percentage = $data['total_lbs_bascula'] / $task_production->total_lbs;

            if ($percentage < ($task_production->line_sku->accepted_percentage / 100)) {
                $task_production->is_minimum_require = false;
                $task_production->is_justified = false;
            } else {
                $task_production->is_minimum_require = true;
                $task_production->is_justified = true;
            };

            $lbs_produced = $data['total_tarimas'] ? (($data['total_tarimas'] * $task_production->line_sku->sku->boxes_pallet) * $task_production->line_sku->sku->presentation) : $data['total_lbs_bascula'];

            $task_production->finished_tarimas = $data['total_tarimas'] ?? 0;
            $task_production->total_lbs_bascula = $data['total_lbs_bascula'];
            $task_production->total_lbs_produced = $lbs_produced;
            $task_production->end_date = Carbon::now();
            $task_production->status = 5;
            $task_production->save();

            if ($task_production->total_lbs_bascula < $task_production->total_lbs) {
                $this->emailReturnPackingMaterialService->sendNotification($task_production);
            }


            return response()->json('Tarea Cerrada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function TaskDetails(string $id)
    {
        $task_production = TaskProductionPlan::find($id);

        $data = new TaskProductionPlanDetailResource($task_production);
        return response()->json($data, 200);
    }

    public function TakePerformance(Request $request, string $id)
    {
        $data = $request->validate([
            'tarimas_produced' => 'sometimes',
            'lbs_bascula' => 'required'
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
                'tarimas_produced' => $data['tarimas_produced'] ?? null,
                'lbs_bascula' => $data['lbs_bascula']
            ]);

            return response()->json('Rendimiento Tomado Correctamente', 200);
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

            return response()->json('Prioridad Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangeOperationDate(Request $request, string $id)
    {
        $data = $request->validate([
            'date' => 'required',
            'reason' => 'required'
        ]);


        $payload = JWTAuth::getPayload();
        $user_id = $payload->get('id');
        $role = $payload->get('role');

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }


        $old_date = $task_production->operation_date;
        $today = Carbon::today();
        $new_date = Carbon::parse($data['date']);
        $diff = $today->diffInDays($new_date);
        $week = Carbon::parse($data['date'])->weekOfYear;
        $now_week = Carbon::now()->weekOfYear;

        if ($role != 'admin') {
            if ($today === $new_date) {
                return response()->json([
                    'msg' => 'No puede asignar tareas a el día en curso'
                ], 500);
            }

            if ($diff < 0) {
                return response()->json([
                    'msg' => 'No puede asignar tareas a días anteriores'
                ], 500);
            }

            if ($diff == 1) {
                $limit_hour = Carbon::createFromTime(15, 0, 0);
                $hour = Carbon::now();

                if ($hour > $limit_hour) {
                    return response()->json([
                        'msg' => 'El limite para realizar cambios es a las 3PM'
                    ], 500);
                }
            }

            // if ($week < $now_week || $week > $now_week) {
            //     return response()->json([
            //         'msg' => 'La fecha no se encuentra dentro de la semana de la tarea'
            //     ], 500);
            // }
        }

        try {
            $last_task = TaskProductionPlan::whereDate('operation_date', $data['date'])->where('line_id', $task_production->line_id)->get()->first();

            TaskOperationDateBitacora::create([
                'task_production_plan_id' => $task_production->id,
                'original_date' => $old_date,
                'new_date' => $data['date'],
                'reason' => $data['reason'],
                'user_id' => $user_id
            ]);


            $task_production->operation_date = $data['date'];
            $task_production->save();

            if ($last_task) {
                if ($task_production->employees->count() > 0) {
                    $task_production->employees()->delete();
                }

                foreach ($task_production->employees as $employee) {
                    $employee->bitacoras()->delete();
                    $employee->delete();
                }

                foreach ($last_task->employees as $employee) {
                    TaskProductionEmployee::create([
                        'task_p_id' => $task_production->id,
                        'name' => $employee->name,
                        'code' => $employee->code,
                        'position' => $employee->position
                    ]);
                }
            }

            ProductionOperationChange::create([
                'user_id' => $user_id,
                'task_production_plan_id' => $task_production->id,
            ]);

            return response()->json('Fecha de Operación Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function AssignOperationDate(Request $request, string $id)
    {
        $data = $request->validate([
            'date' => 'required'
        ]);

        $task = TaskProductionPlan::find($id);
        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no encontrada'
            ], 404);
        }

        try {

            $payload = JWTAuth::getPayload();
            $id = $payload->get('id');
            $role = $payload->get('role');

            $today = Carbon::today();
            $new_date = Carbon::parse($data['date']);
            $diff = $today->diffInDays($new_date);
            $week = Carbon::parse($data['date'])->weekOfYear;
            $now_week = Carbon::now()->weekOfYear;


            if ($role != 'admin') {
                if ($today === $new_date) {
                    return response()->json([
                        'msg' => 'No puede asignar tareas a el día en curso'
                    ], 500);
                }

                if ($diff < 0) {
                    return response()->json([
                        'msg' => 'No puede asignar tareas a días anteriores'
                    ], 500);
                }

                if ($diff == 1) {
                    $limit_hour = Carbon::createFromTime(15, 0, 0);
                    $hour = Carbon::now();

                    if ($hour > $limit_hour) {
                        return response()->json([
                            'msg' => 'El limite para realizar cambios es a las 3PM'
                        ], 500);
                    }
                }

                // if ($week < $now_week || $week > $now_week) {
                //     return response()->json([
                //         'msg' => 'La fecha no se encuentra dentro de la semana de la tarea'
                //     ], 500);
                // }
            }

            $task->operation_date = $data['date'];
            $task->save();

            ProductionOperationChange::create([
                'user_id' => $id,
                'task_production_plan_id' => $task->id,
            ]);

            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error'
            ], 500);
        }
    }

    public function CreateNewTaskProduction(CreateTaskProductionRequest $request, String $id)
    {
        $data = $request->validated();

        $weekly_plan = WeeklyProductionPlan::find($id);

        if (!$weekly_plan) {
            return response()->json([
                'msg' => 'Plan Semanal No Encontrado'
            ], 404);
        }

        $payload = JWTAuth::getPayload();
        $user_id = $payload->get('id');
        $role = $payload->get('role');

        try {

            $today = Carbon::today();
            $new_date = Carbon::parse($data['data'][0]['operation_date']);
            $limit_hour = Carbon::createFromTime(15, 0, 0);
            $hour = Carbon::now();
            $diff = $today->diffInDays($new_date);
            $week = Carbon::parse($data['data'][0]['operation_date'])->weekOfYear;
            $now_week = Carbon::now()->weekOfYear;

            if ($role != 'admin') {
                if ($today === $new_date) {
                    return response()->json([
                        'msg' => 'No puede asignar tareas a el día en curso'
                    ], 500);
                }

                if ($diff < 0) {
                    return response()->json([
                        'msg' => 'No puede asignar tareas a días anteriores'
                    ], 500);
                }

                if ($diff == 1) {
                    $limit_hour = Carbon::createFromTime(15, 0, 0);
                    $hour = Carbon::now();

                    if ($hour > $limit_hour) {
                        return response()->json([
                            'msg' => 'El limite para realizar cambios es a las 3PM'
                        ], 500);
                    }
                }

                // if ($week < $now_week || $week > $now_week) {
                //     return response()->json([
                //         'msg' => 'La fecha no se encuentra dentro de la semana de la tarea'
                //     ], 500);
                // }
            }


            foreach ($data['data'] as $task) {
                $operation_date = Carbon::parse($task['operation_date']);


                if ($operation_date->lessThan($today)) {
                    return response()->json([
                        'msg' => 'No puede programar tareas a fechas pasadas'
                    ], 500);
                }

                $line = Line::find($task['line_id']);
                $sku = StockKeepingUnit::find($task['sku_id']);
                $line_sku = LineStockKeepingUnits::where('line_id', $line->id)->where('sku_id', $sku->id)->get()->first();


                $task_line = TaskProductionPlan::where('line_id', $line->id)->where('weekly_production_plan_id', $weekly_plan->id)->get()->last();
                $total_hours =  $line_sku->lbs_performance ? ($task['total_lbs'] / $line_sku->lbs_performance) : null;

                $new_task = TaskProductionPlan::create([
                    'line_id' => $line->id,
                    'weekly_production_plan_id' => $weekly_plan->id,
                    'operation_date' => $task['operation_date'] ?? null,
                    'total_hours' => round($total_hours, 2),
                    'line_sku_id' => $line_sku->id,
                    'status' =>  1,
                    'destination' => $task['destination'],
                    'total_lbs' => $task['total_lbs']
                ]);

                if ($task_line) {
                    foreach ($task_line->employees as $employee) {
                        TaskProductionEmployee::create([
                            'task_p_id' => $new_task->id,
                            'name' => $employee->name,
                            'code' => $employee->code,
                            'position' => $employee->position
                        ]);
                    }
                }

                ProductionOperationChange::create([
                    'user_id' => $user_id,
                    'task_production_plan_id' => $new_task->id,
                ]);
            }

            return response()->json('Información actualizada correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function CreateAssignee(CreateAssigmentsRequest $request, string $id)
    {
        $data = $request->validated();
        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Tarea de Producción No Encontrada'
            ], 404);
        }

        try {
            $tasks = TaskProductionPlan::where('line_id', $task_production->line_id)
                ->whereDate('operation_date', $task_production->operation_date)
                ->whereNull('start_date')
                ->whereNull('end_date')
                ->get();

            if (!empty($data['data'])) {
                foreach ($data['data'] as $newEmployee) {
                    $position = null;

                    if ($newEmployee['position_id']) {
                        $position = LinePosition::find($newEmployee['position_id']);
                    }

                    foreach ($tasks as $task) {
                        TaskProductionEmployee::create([
                            'task_p_id' => $task->id,
                            'name' => $newEmployee['name'],
                            'code' => $newEmployee['code'],
                            'position' => $position ? $position->name : $newEmployee['old_position']
                        ]);
                    }
                }
                $this->emailCreateAssigneeService->sendNotification($data['data'], $task);
            }

            return response()->json('Asignaciónes creadas correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }


    public function FinishedTaskDetails(string $id)
    {
        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Tarea de Producción No Encontrada'
            ], 404);
        }

        $info = new FinishedTaskProductionResource($task_production);

        return response()->json($info, 200);
    }

    public function Unassign(Request $request, string $id)
    {
        $data = $request->validate([
            'reason' => 'required',
            'assignments' => 'required'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'errors' => 'Tarea de Producción no Encontrada'
            ], 404);
        }

        try {
            $payload = JWTAuth::getPayload();
            $user_id = $payload->get('id');


            $unassign_note = TaskProductionUnassign::create([
                'task_p_id' => $task_production->id,
                'user_id' => $user_id,
                'reason' => $data['reason'],
            ]);

            foreach ($data['assignments'] as $assignment_id) {
                $task_employee = TaskProductionEmployee::find($assignment_id);
                $line = $task_production->line_sku->line->code;
                $employees = TaskProductionEmployee::whereHas('TaskProduction', function ($query) use ($line) {
                    $query->where('start_date', null)->where('end_date', null);
                    $query->whereHas('line', function ($query) use ($line) {
                        $query->where('code', $line);
                        $query->whereDate('operation_date', Carbon::now());
                    });
                })->where('position', $task_employee->position)->get();

                foreach ($employees as $employee) {
                    $employee->bitacoras()->delete();
                }
                $employees->each->delete();

                $hours = round($task_production->start_date->diffInHours(Carbon::now()), 2);

                if (!$task_employee) {
                    return response()->json([
                        'errors' => 'La asignación no existe'
                    ], 404);
                }
                TaskProductionUnassignAssignment::create([
                    'task_p_unassign_id' => $unassign_note->id,
                    'assignment_id' => $task_employee->id,
                    'hours' => $hours
                ]);
            }

            return response()->json('Nota Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangeStatus(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required'
        ]);

        try {
            $task = TaskProductionPlan::find($id);
            if (!$task) {
                return response()->json([
                    'msg' => 'Tarea no Encontrada'
                ], 404);
            }

            $task->status = $data['status'];
            $task->save();

            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al actualizar'
            ], 500);
        }
    }

    public function TaskDevolutionDetails(string $id)
    {
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 200);
        }

        if (!$task->end_date) {
            return response()->json([
                'msg' => 'La tarea no ha sido terminada'
            ], 200);
        }

        return new TaskPackingMaterialReturnDetailsResource($task);
    }

    public function ConfirmAssignments(ChangeAssigmentsRequest $request, string $id)
    {
        $data = $request->validated();
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            if (!empty($data['data']) && !$data['previous_config']) {
                foreach ($data['data'] as $change) {
                    $assignment = TaskProductionEmployee::find($change['old_employee']['id']);
                    $NewChange = TaskProductionEmployeesBitacora::create([
                        "assignment_id" => $assignment->id,
                        "original_name" => $assignment->name,
                        "original_code" => $assignment->code,
                        "original_position" => $assignment->position,
                        "new_name" => $change['new_employee']['name'],
                        "new_code" => $change['new_employee']['code'],
                        "new_position" => $change['new_employee']['position']
                    ]);

                    EmployeeTransfer::create([
                        'change_employee_id' => $NewChange->id,
                        'confirmed' => false
                    ]);

                    $assignment->name = $NewChange->new_name;
                    $assignment->code = $NewChange->new_code;
                    $assignment->position = $NewChange->new_position;
                    $assignment->save();
                }
                $this->emailService->sendNotification($data['data'], $task);
            } else if ($data['previous_config']) {
                $task->employees()->delete();
               $last_task = TaskProductionPlan::where('line_id', $task->line_id)
                    ->whereNotNull('start_date')
                    ->whereNotNull('end_date')
                    ->orderByDesc('end_date')
                    ->first();


                foreach ($last_task->employees as $employee) {
                    TaskProductionEmployee::create([
                        'task_p_id' => $task->id,
                        'name' => $employee->name,
                        'code' => $employee->code,
                        'position' => $employee->position
                    ]);
                }
            }


            $task->status = 3;
            $task->save();
            return response()->json('Asignación Confirmada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function TaskActiveEmployees(string $id)
    {
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $employees = $task->employees;
            $employees = $employees->filter(function ($employee) {
                if (!$employee->unAssigned) {
                    return $employee;
                }
            });

            return TaskProductionEmployeeResource::collection($employees);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
    public function TaskReprogramDetails(string $id)
    {
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $data = [
                'id' => strval($task->id),
                'line' => $task->line->name,
                'sku' => $task->line_sku->sku->product_name,
                'line_id' => strval($task->line_id),
                'sku_id' => strval($task->line_sku->sku->id),
                'total_lbs' => $task->total_lbs,
                'destination' => $task->destination ?? 'SIN DESTINO ASOCIADO'
            ];
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }

        return response()->json($data);
    }

    public function destroy(string $id)
    {
        $task = TaskProductionPlan::find($id);

        try {
            $task->employees()->delete();
            $task->productionChanges()->delete();
            $task->operationDateChanges()->delete();
            $task->delete();
            return response()->json('Tarea eliminada', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UnassignTaskProduction(string $id)
    {
        $task = TaskProductionPlan::find($id);

        try {
            if (!$task) {
                return response()->json([
                    'msg' => 'Tarea No Encontrada'
                ], 404);
            }
            $task->operation_date = null;
            $task->save();

            return response()->json('Tarea Actualizada', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function DeleteTaskProductionAssigments(Request $request, string $id)
    {
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task->employees()->delete();

            return response()->json('Asignaciones eliminadas', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function GetEditDetails(Request $request, string $id)
    {
        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task  = new TaskProductionEditDetailsResource($task);
            return response()->json($task);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function UpdateTaskProductionStatus(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required'
        ]);

        $task = TaskProductionPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task->status = $data['status'];
            $task->save();

            return response()->json('Estado de Tarea Actualizado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
