<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
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
            'tasks' => ['required', 'array'],
            'tasks.*.task_id' => ['required', 'numeric', 'exists:task_weekly_plans,id']
        ];
    }

    public function messages(): array
    {
        return [
            'tasks.required' => 'Las tareas son requeridas',
            'tasks.array' => 'Las tareas deben de ser un arreglo',
            'tasks.*.task_id.required' => 'El id de la tarea es requerida',
            'tasks.*.task_id.numeric' => 'El id de la tarea debe de ser un dato númerico',
            'tasks.*.task_id.exists' => 'El id de la tarea no existe'
        ];
    }
}
