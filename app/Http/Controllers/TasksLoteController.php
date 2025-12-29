<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskWeeklyPlanRequest;
use App\Http\Requests\EditTaskWeeklyPlanRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\EditTaskWeeklyPlanResource;
use App\Http\Resources\TaskWeeklyPlanDetailsResource;
use App\Http\Resources\TaskWeeklyPlanResource;
use App\Models\BinnacleTaskWeeklyPlan;
use App\Models\EmployeeTask;
use App\Models\FincaGroup;
use App\Models\Lote;
use App\Models\PartialClosure;
use App\Models\TaskInsumos;
use App\Models\TaskWeeklyPlan;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Error;
use Tymon\JWTAuth\Facades\JWTAuth;

class TasksLoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $payload = JWTAuth::getPayload();
        $role = $payload->get('role');

        $query = TaskWeeklyPlan::query();

        $query->whereHas('cdp', function ($q) use ($request) {
            $q->whereHas('lote', function ($qq) use ($request) {
                $qq->where('name', $request->query('lote'));
            });
        });
        $query->where('weekly_plan_id', $request->query('weekly_plan'));

        $task_without_filter = $query->get()->first();

        if ($request->query('name')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('name') . '%');
            });
        }

        if ($request->query('code')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('code', $request->query('name'));
            });
        }

        if ($request->query('task_type')) {
            $query->where('extraordinary', $request->query('task_type'));
        }

        if ($role !== 'admin' && $role !== 'adminagricola') {
            $query->where(function ($query) use ($today) {
                $query->whereDate('operation_date', $today)->where('end_date', null);
                $query->OrwhereNot('start_date', null)->where('end_date', null)
                    ->orWhereHas('closures', function ($q) {
                        $q->where('end_date', null);
                    });
            })->get();
        }

        $tasks = $query->get();

        if ($role !== 'admin' && $role !== 'adminagricola') {
            $tasks_filterd = $tasks->filter(function ($task) {
                if ($task->insumos->count() === 0) {
                    return $task;
                } else {
                    $flag = $task->prepared_insumos ? true : false;
                    if ($flag) {
                        return $task;
                    }
                }
            });
        } else {
            $tasks_filterd = $tasks;
        }


        return [
            'week' => $task_without_filter->plan->week,
            'finca' => $task_without_filter->plan->finca->name,
            'lote' => $task_without_filter->cdp->lote->name,
            'data' => TaskWeeklyPlanResource::collection($tasks_filterd),
        ];
    }

    public function store(CreateTaskWeeklyPlanRequest $request)
    {
        $data = $request->validated();

        try {
            $task_weekly_plan = TaskWeeklyPlan::create([
                'weekly_plan_id' => $data['weekly_plan_id'],
                'lote_plantation_control_id' => 1,
                'tarea_id' => $data['tarea_id'],
                'workers_quantity' => $data['hours'] > 8 ? max(1, floor($data['hours'] / 8)) : 1,
                'slots' => $data['slots'],
                'budget' => $data['budget'],
                'hours' => $data['hours'],
                'extraordinary' => true,
                'operation_date' => $data['operation_date'],
                'plantation_control_id' => $data['cdp_id'],
            ]);

            if (isset($data['insumos']) && count($data['insumos']) > 0) {
                foreach ($data['insumos'] as $insumo) {
                    TaskInsumos::create([
                        'insumo_id' => $insumo['insumo_id'],
                        'task_weekly_plan_id' => $task_weekly_plan->id,
                        'assigned_quantity' => $insumo['quantity'],
                    ]);
                }
            }

            return response()->json([
                'statusCode' => 201,
                'message' => 'Tarea Creada Correctamente'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $data = TaskWeeklyPlan::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'TaskWeeklyPlan not found'
            ], 404);
        }

        return response()->json([
            'data' => new TaskWeeklyPlanResource($data)
        ]);
    }

    public function PartialClose(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            PartialClosure::create([
                'task_weekly_plan_id' => $task->id,
                'start_date' => Carbon::now(),
            ]);

            return response()->json('Tarea Pausada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al crear el cierre parcial'
            ], 500);
        }
    }

    public function PartialCloseOpen(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }


        try {
            $registro = $task->closures->last();
            $registro->update([
                'end_date' => Carbon::now(),
            ]);

            return response()->json('Tarea Reaperturada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al reaperturar la tarea'
            ], 500);
        }
    }

    public function CloseAssigment(Request $request, string $id)
    {
        $task = TaskWeeklyPlan::find($id);
        if (!$task) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            $date = Carbon::now();
            if (!$request->query('dron')) {

                foreach ($task->group->employees as $employee) {
                    EmployeeTask::create([
                        'code' => $employee->code,
                        'name' => $employee->name,
                        'employee_id' => $employee->id,
                        'task_weekly_plan_id' => $task->id
                    ]);
                }
            } else {
                $task->use_dron = true;
            }

            $task->start_date = $date;
            $task->save();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Tarea Iniciada Correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function TaskDetail(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        return new TaskWeeklyPlanDetailsResource($task);
    }

    public function CloseTask(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            $task->end_date = Carbon::now();
            $task->save();

            return response()->json('Tarea Cerrada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al cerrar la tarea'
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea No Encontrada'
            ], 404);
        }

        try {
            $task->insumos()->delete();
            $task->weeklyPlanChanges()->delete();
            $task->delete();

            return response()->json('Tarea Eliminada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al eliminar la tarea'
            ], 500);
        }


        return response()->json([
            'message' => 'Task Deleted'
        ]);
    }

    public function update(EditTaskWeeklyPlanRequest $request, string $id)
    {
        $data = $request->validated();
        $task = TaskWeeklyPlan::find($id);

        $start_date = $task->start_date;
        $end_date = $task->end_date;

        if ($start_date && ($data['start_date'] ?? false)) {
            $draft_start_date = $data['start_date'] . ' ' . $data['start_time'];
            $start_date = Carbon::parse($draft_start_date);
        }

        if ($end_date && ($data['end_date'] ?? false)) {
            $draft_end_date = $data['end_date'] . ' ' . $data['end_time'];
            $end_date = Carbon::parse($draft_end_date);
        }


        try {
            $task->budget = $data['budget'];
            $task->start_date = $start_date ?? null;
            $task->end_date = $end_date ?? null;
            $task->hours = $data['hours'];
            $task->slots = $data['slots'];
            $task->workers_quantity = $data['slots'];


            if ($task->weekly_plan_id != $data['weekly_plan_id']) {
                $dest = WeeklyPlan::find($data['weekly_plan_id']);

                if ($dest->finca->id != $task->plan->finca->id) {
                    throw new Error("La finca no coincide con el lote de la tarea");
                }
                BinnacleTaskWeeklyPlan::create([
                    'task_weekly_plan_id' => $task->id,
                    'from_plan' => $task->plan->id,
                    'to_plan' => $dest->id
                ]);
                $task->weekly_plan_id = $data['weekly_plan_id'];
                $task->operation_date = null;
            }

            $task->save();
            return response()->json('Tarea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
            ], 400);
        }
    }

    public function EraseAssignationTask(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no Encontrada'
            ], 404);
        }

        try {
            $task->start_date = null;
            $task->end_date = null;
            $task->employees()->delete();
            $task->slots = $task->workers_quantity;
            $task->save();

            return response()->json('Asignación Eliminada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Hubo un error al borrar la asignación'
            ], 500);
        }


        return response()->json([
            'message' => 'Task Cleaned'
        ]);
    }

    public function RegisterInsumos(Request $request)
    {

        $data = $request->validate([
            'insumos' => 'required'
        ]);

        foreach ($data['insumos'] as $insumo) {
            try {
                $assignment = TaskInsumos::find($insumo['id']);
                $assignment->used_quantity = $insumo['used_quantity'];
                $assignment->save();
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => $data
                ], 500);
            }
        }

        return response()->json('Insumos Registrados Correctamente');
    }

    public function GetTaskForEdit(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea de Lote No Encontrada'
            ], 404);
        }

        return new EditTaskWeeklyPlanResource($task);
    }

    public function ChangeOperationDate(Request $request)
    {
        $data = $request->validate([
            'date' => 'required',
            'group_id' => 'required',
            'tasks' => 'required'
        ]);

        try {
            $week = Carbon::parse($data['date'])->weekOfYear;
            $now_week = Carbon::now()->weekOfYear;
            $group = FincaGroup::find($data['group_id']);

            if ($week < $now_week || $week > $now_week) {
                return response()->json([
                    'msg' => 'La fecha no se encuentra dentro de la semana de la tarea'
                ], 500);
            }

            if (!$group) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Grupo no encontrado'
                ], 404);
            }

            foreach ($data['tasks'] as $id) {
                $task_weekly_plan = TaskWeeklyPlan::find($id);
                $task_weekly_plan->operation_date = $data['date'];
                $task_weekly_plan->finca_group_id = $group->id;
                $task_weekly_plan->save();
            }

            return response()->json([
                'statusCode' => 200,
                'message' => 'Tareas Actualizadas Correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function ChangeGroupAssignment(UpdateGroupRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            $group = FincaGroup::find($id);

            if (!$group) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'El grupo no existe'
                ], 404);
            }

            foreach ($data['tasks'] as $task) {
                $register = TaskWeeklyPlan::find($task['task_id']);
                $register->finca_group_id = $group->id;
                $register->save();
            }

            return response()->json([
                'statusCode' => 200,
                'message' => 'Tareas Actualizadas Correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function PreparedInsumos(string $id)
    {
        $task = TaskWeeklyPlan::find($id);

        if (!$task) {
            return response()->json([
                'msg' => 'Tarea no encontrada'
            ], 404);
        }

        try {
            if ($task->insumos->count() > 0) {
                $task->prepared_insumos = true;
                $task->save();
            } else {
                return response()->json([
                    'msg' => 'La tarea no cuenta con insumos relacionados'
                ], 422);
            }
            return response()->json('Información actualizada correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
