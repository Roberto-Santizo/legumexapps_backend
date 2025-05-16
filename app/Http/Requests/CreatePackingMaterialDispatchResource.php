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
            'observations' => ['sometimes'],
            'received_by_boxes' => ['required'],
            'received_by_signature_boxes' => ['required'],
            'received_by_bags' => ['required'],
            'received_by_signature_bags' => ['required'],
            'user_signature' => ['required'],
            'task_production_plan_id' => ['required', 'exists:task_production_plans,id'],
            'reference' => ['required'],
            'quantity_boxes' => ['required'],
            "quantity_bags" => ['required'],
            "quantity_inner_bags" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'received_by_boxes.required' => 'El nombre del receptor de cajas es obligatorio',
            'received_by_signature_boxes.required' => 'La firma del receptor de cajas es obligatoria',
            'received_by_bags.required' => 'El nombre del receptor de bolsas es obligatoria',
            'received_by_signature_bags.required' => 'La firma del receptor de bolsas es obligatoria',
            'user_signature.required' => 'La firma es requerida',
            'task_production_plan_id.required' => 'La referencia de producción es requerida',
            'task_production_plan_id.exists' => 'La tarea de producción no existe',
            'reference.required' => 'La referencia es obligatoria',
            'quantity_boxes' => 'La cantidad de cajas es obligatoria',
            "quantity_bags" => 'La cantidad de bolsas es obligatoria',
            "quantity_inner_bags" => 'La cantidad de bolsas inner es obligatoria'

        ];
    }
}
