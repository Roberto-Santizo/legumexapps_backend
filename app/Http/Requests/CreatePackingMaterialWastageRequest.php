<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackingMaterialWastageRequest extends FormRequest
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
            'task_p_id' => ['required','exists:task_production_plans,id'],
            'packing_material_id' => ['required','exists:packing_materials,id'],
            'quantity' => ['required','numeric'],
            'lote' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'task_production_plan_id.required' => 'La tarea de producción es obligatoria',
            'task_production_plan_id.exists' => 'La tarea de producción no existe',
            'packing_material_id.required' => 'El item de material de empaque es obligatorio',
            'packing_material_id.exists' => 'El item de material de empaque no existe',
            'quantity.required' => 'La cantidad es requerida',
            'quantity.numeric' => 'La cantidad debe de ser un dato numerico',
            'lote.required' => 'El lote es requerido'
        ];
    }
}
