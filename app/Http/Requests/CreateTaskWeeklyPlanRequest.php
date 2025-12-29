<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskWeeklyPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "tarea_id" =>  ['required', 'numeric', 'exists:tareas,id'],
            "budget" =>  ['required', 'numeric'],
            "hours" =>  ['required', 'numeric'],
            "slots" =>  ['required', 'numeric'],
            "cdp_id" => ['required', 'numeric', 'exists:plantation_controls,id'],
            "weekly_plan_id" => ['required', 'numeric', 'exists:weekly_plans,id'],
            "operation_date" => ['required'],
            "insumos" => ['array','sometimes'],
            "insumos.*.insumo_id" => ['required', 'numeric', 'exists:insumos,id'],
            "insumos.*.quantity" => ['required', 'numeric'],
            "finca_group_id" => ['required', 'numeric', 'exists:finca_groups,id']
        ];
    }

    public function messages(): array
    {
        return [
            "tarea_id.required" => 'La tarea es requerida',
            "tarea_id.exists" => 'La tarea no existe',
            "budget.required" => 'El presupuesto es requerido',
            "hours.required" => 'Las horas son requeridas',
            "slots.required" => 'Los turnos son requeridos',
            "cdp_id.required" => 'El CDP es requerido',
            "cdp_id.exists" => 'El CDP no existe',
            "weekly_plan_id.required" => 'El plan semanal es requerido',
            "weekly_plan_id.exists" => 'El plan semanal no existe',
            "operation_date.required" => 'La fecha de operación es requerida',
            "finca_group_id.required" => 'El grupo de finca es requerido',
            "finca_group_id.exists" => 'El grupo de finca no existe',
        ];
    }
}
