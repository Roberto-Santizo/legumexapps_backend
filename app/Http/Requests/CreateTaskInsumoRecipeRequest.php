<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskInsumoRecipeRequest extends FormRequest
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
            'task_guideline_id' => ['required', 'exists:task_guidelines,id'],
            'insumo_id' => ['required', 'exists:insumos,id'],
            'quantity' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'task_guideline_id.required' => "La tarea guía es requerida",
            'task_guideline_id.exists' => "La tarea guía no existe",
            'insumo.required' => "El insumo es requerido",
            'insumo.exists' => 'El insumo no existe',
            'quantity.required' => 'La cantidad es requerida',
            'quantity.numeric' => 'La cantidad debe de ser un dato númerico'

        ];
    }
}
