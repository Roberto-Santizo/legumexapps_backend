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
            'task_id' =>    ['required', 'exists:tareas,id'],
            'recipe_id' =>  ['required', 'exists:recipes,id'],
            'crop_id' =>    ['required', 'exists:crops,id'],
            'hours' =>      ['required'],
            'week' =>       ['required'],
            'insumos' =>    ['sometimes', 'array'],
            'insumos.*.insumo_id' => ['required', 'exists:insumos,id'],
            'insumos.*.quantity' =>  ['required', 'numeric', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required' =>       'La tarea es requerida',
            'recipe_id.required' =>     'La temporada es requerida',
            'variety_id.required' =>    'El cultivo es requerido',
            'hours.required' =>         'Las horas son requeridas',
            'week' =>                   'La semana de aplicación es requerida',
            'task_id.exists' =>         'La tarea no existe',
            'recipe_id.exists' =>       'La temporada no existe',
            'variety_id.exists' =>      'El cultivo no existe',
            'insumos.array' =>           'Los insumos deben de ser un arreglo',
            'insumos.*.insumos_id.required' =>  'El insumo es requerido',
            'insumos.*.insumos_id.exists' =>    'El insumo no existe',
            'insumos.*.quantity.required' =>    'La cantidad es requerida',
            'insumos.*.quantity.numeric' =>     'La cantidad debe de ser un valor númerico',
            'insumos.*.quantity.min' =>         'La cantidad debe de ser mayor a 0',

        ];
    }
}
