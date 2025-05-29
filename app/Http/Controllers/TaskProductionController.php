<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeAssignmentRequest;
use App\Http\Resources\FinishedTaskProductionResource;
use App\Http\Resources\TaskPackingMaterialReturnDetailsResource;
use App\Http\Resources\TaskProductionPlanDetailResource;
use App\Http\Resources\TaskProductionPlanDetailsResource;
use App\Http\Resources\TaskProductionPlanResource;
use App\Models\EmployeeTransfer;
use App\Models\Line;
use App\Models\LinePosition;
use App\Models\LineStockKeepingUnits;
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
use Illuminate\Http\Request;

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

    public function ChangeAssignment(ChangeAssignmentRequest $request)
    {
        $data = $request->validated();

        $assignment = TaskProductionEmployee::find($data['assignment_id']);

        if (!$assignment) {
            return response()->json([
                'msg' => 'Asignación no encontrada'
            ], 404);
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

            $line = $assignment->TaskProduction->line_sku->line->code;

            $employees = TaskProductionEmployee::whereHas('TaskProduction', function ($query) use ($line) {
                $query->where('start_date', null)->where('end_date', null);
                $query->whereHas('line', function ($query) use ($line) {
                    $query->where('code', $line);
                    $query->whereDate('operation_date', Carbon::now());
                });
            })->where('position', $change->original_position)->get();

            foreach ($employees as $employee) {
                TaskProductionEmployeesBitacora::create([
                    "assignment_id" => $employee->id,
                    "original_name" => $assignment->name,
                    "original_code" => $assignment->code,
                    "original_position" => $assignment->position,
                    "new_name" => $data['new_name'],
                    "new_code" => $data['new_code'],
                    "new_position" => $data['new_position']
                ]);

                $employee->name = $change->new_name;
                $employee->code = $change->new_code;
                $employee->position = $change->new_position;
                $employee->save();
            }

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
            $task_production->status = 2;
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
            $total_boxes = 0;
            $lbs_teoricas = 0;
            $performance_hours = 0;
            $percentage = 0;

            if ($task_production->line_sku->lbs_performance) {
                $total_boxes = $data['total_tarimas'] * $task_production->line_sku->sku->boxes_pallet;
                $lbs_teoricas = $total_boxes * $task_production->line_sku->sku->presentation;
                $performance_hours = $lbs_teoricas / $task_production->line_sku->lbs_performance;
                $percentage = $performance_hours / $task_production->total_hours;
            }


            if ($task_production->line_sku->lbs_performance && $percentage < ($task_production->line_sku->accepted_percentage / 100)) {
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
            $task_production->status = 3;
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

        return new TaskProductionPlanDetailResource($task_production);
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

        $user = $request->user();
        $role = $user->getRoleNames()->first();

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Task Production Plan Not Found'
            ], 404);
        }

        $new_date = Carbon::parse($data['date']);
        $old_date = $task_production->operation_date;
        $today = Carbon::today();

        $limit_hour = Carbon::createFromTime(15, 0, 0);
        $hour = Carbon::now();

        if ($role !== 'admin') {
            if (!$hour->lessThan($limit_hour)) {
                return response()->json([
                    'msg' => 'No se puede programar la tarea, la hora limite para poder programar son las 3:00 PM'
                ], 500);
            }

            if ($new_date->weekOfYear != $today->weekOfYear) {
                return response()->json([
                    'msg' => 'No puede mover tareas fuera de la semana actual'
                ], 500);
            }
        }

        try {
            $last_task = TaskProductionPlan::whereDate('operation_date', $data['date'])->where('line_id', $task_production->line_id)->get()->first();

            TaskOperationDateBitacora::create([
                'task_production_plan_id' => $task_production->id,
                'original_date' => $old_date,
                'new_date' => $data['date'],
                'reason' => $data['reason'],
                'user_id' => $user->id
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
            $task->operation_date = $data['date'];
            $task->save();

            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error'
            ], 500);
        }
    }

    public function CreateNewTaskProduction(Request $request)
    {
        $data = $request->validate([
            'line_id' => 'required|exists:lines,id',
            'operation_date' => 'required',
            'sku_id' => 'required|exists:stock_keeping_units,id',
            'total_lbs' => 'required',
            'destination' => 'required'
        ]);

        $role = $request->user()->getRoleNames()->first();
        $line = Line::find($data['line_id']);
        $sku = StockKeepingUnit::find($data['sku_id']);
        $line_sku = LineStockKeepingUnits::where('line_id', $line->id)->where('sku_id', $sku->id)->get()->first();

        $operation_date = Carbon::parse($data['operation_date']);
        $today = Carbon::today();
        $limit_hour = Carbon::createFromTime(15, 0, 0);
        $hour = Carbon::now();

        if ($role != 'admin') {
            if (!$hour->lessThan($limit_hour)) {
                return response()->json([
                    'msg' => 'No se puede programar la tarea, la hora limite para poder programar son las 3:00 PM'
                ], 500);
            }

            if ($operation_date->lessThan($today)) {
                return response()->json([
                    'msg' => 'No puede programar tareas a fechas pasadas'
                ], 500);
            }
        }

        try {
            $task_line = TaskProductionPlan::where('line_id', $line->id)->get()->last();
            $weekly_production_plan = WeeklyProductionPlan::all()->last();

            $task_week = TaskProductionPlan::where('line_id', $line->id)->whereDate('operation_date', $data['operation_date'])->get()->last();
            $total_hours =  $line_sku->lbs_performance ? ($data['total_lbs'] / $line_sku->lbs_performance) : null;

            $new_task = TaskProductionPlan::create([
                'line_id' => $line->id,
                'weekly_production_plan_id' => $weekly_production_plan->id,
                'operation_date' => $data['operation_date'],
                'total_hours' => round($total_hours, 2),
                'line_sku_id' => $line_sku->id,
                'priority' => $task_week ? $task_week->priority + 1 : 1,
                'status' => $task_line ? 1 : 0,
                'destination' => $data['destination'],
                'total_lbs' => $data['total_lbs']
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

                return response()->json('Tarea Creada Correctamente', 200);
            } else {
                return response()->json('Tarea Creada Correctamente, Pendiente de Asignación de Personal', 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function CreateAssignee(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'old_position' => 'required',
            'position_id' => 'required|exists:line_positions,id'
        ]);

        $task_production = TaskProductionPlan::find($id);

        if (!$task_production) {
            return response()->json([
                'msg' => 'Tarea de Producción No Encontrada'
            ], 404);
        }

        try {
            $tasks = TaskProductionPlan::where('line_id', $task_production->line_id)->whereDate('operation_date', $task_production->operation_date)->where('start_date', null)->where('end_date', null)->get();
            $position = LinePosition::find($data['position_id']);

            foreach ($tasks as $task) {
                $newAssignee = TaskProductionEmployee::create([
                    'task_p_id' => $task->id,
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'position' => $position->name
                ]);
            }

            $newAssignee->old_position = $data['old_position'];

            $this->emailCreateAssigneeService->sendNotification($newAssignee);
            return response()->json('Asignación creada correctamente', 200);
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
            $user = $request->user();

            $unassign_note = TaskProductionUnassign::create([
                'task_p_id' => $task_production->id,
                'user_id' => $user->id,
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
}
