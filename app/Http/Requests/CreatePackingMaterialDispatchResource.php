<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackingMaterialDispatchResource extends FormRequest
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
            'task_production_plan_id' => ['sometimes', 'exists:task_production_plans,id'],
            'reference' => ['required', 'unique:packing_material_transactions,reference'],
            'responsable' => ['required', 'string'],
            'responsable_signature' => ['required'],
            'user_signature' => ['required'],
            'observations' => ['sometimes'],
            'items' => ['required', 'array'],
            'items.*.packing_material_id' => ['required', 'exists:packing_materials,id'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.lote' => ['required', 'string'],
            'items.*.destination' => ['sometimes'],
            'wastages' => ['sometimes', 'array'],
            'wastages.*.packing_material_id' => ['required', 'exists:packing_materials,id'],
            'wastages.*.quantity' => ['required'],
            'wastages.*.lote' => ['required'],
            'type' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'task_production_plan_id.exists' => 'La tarea de producción no existe',
            'reference.required' => 'El campo referencia es obligatorio',
            'reference.unique' => 'La referencia ya tiene relacionado un registro',
            'user_signature.required' => 'La firma de la persona que despacha es obligatoria',
            'responsable.required' => 'El nombre del resposable de recepción es requerido',
            'responsable_signature.required' => 'La firma del resposable es requerida',
            'items.required' => 'Debe relacionar al menos un item',
            'items.*.packing_material_id.required' => 'El item de referencia es obligatoria',
            'items.*.packing_material_id.exists' => 'El item seleccionado no existe',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.numeric' => 'La cantidad debe ser un número',
            'items.*.lote.required' => 'El lote es obligatorio',
        ];
    }
}
