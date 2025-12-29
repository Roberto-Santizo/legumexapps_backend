<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskCropWeeklyPlanRequest extends FormRequest
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
            'cdp_id' => ['required', 'numeric', 'exists:plantation_controls,id'],
            'tarea_id' => ['required', 'numeric', 'exists:tareas,id'],
            'weekly_plan_id' => ['required', 'numeric', 'exists:weekly_plans,id'],
        ];
    }

    public function messages()
    {
        return [
            'cdp_id.required' => 'El CDP es requerido',
            'cdp_id.numeric' => 'El campo cdp_id debe ser un número.',
            'cdp_id.exists' => 'El CDP seleccionado no es válido.',

            'tarea_id.required' => 'La tarea es requerida',
            'tarea_id.numeric' => 'El campo tarea_id debe ser un número.',
            'tarea_id.exists' => 'La tarea seleccionada no es válida.',

            'weekly_plan_id.required' => 'El plan semanal es requerido.',
            'weekly_plan_id.numeric' => 'El campo plan debe ser un número.',
            'weekly_plan_id.exists' => 'El plan semanal seleccionado no es válido.',
        ];
    }
}
