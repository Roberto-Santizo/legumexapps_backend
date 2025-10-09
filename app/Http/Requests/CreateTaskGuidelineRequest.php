<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskGuidelineRequest extends FormRequest
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
            'task_id' => ['required', 'exists:tareas,id'],
            'recipe_id' => ['required','exists:recipes,id'],
            'crop_id' => ['required', 'exists:crops,id'],
            'budget' => 'required',
            'hours' => 'required',
            'week' => 'required'
        ];
    }

    public function messages() : array
    {
        return [ 
            'task_id.required' => 'La tarea es requerida',
            'recipe_id.required' => 'La temporada es requerida',
            'variety_id.required' => 'El cultivo es requerido',
            'budget.required' => 'El presupuesto es requerido',
            'hours.required' => 'Las horas son requeridas',
            'week' => 'La semana de aplicación es requerida',
            'task_id.exists' => 'La tarea no existe',
            'recipe_id.exists' => 'La temporada no existe',
            'variety_id.exists' => 'El cultivo no existe'
        ];
    }
}
