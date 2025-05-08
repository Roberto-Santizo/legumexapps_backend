<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackingMaterialReceptionRequest extends FormRequest
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
            "supervisor_name" => 'required',
            "invoice_date" => 'required',
            "observations" => 'sometimes',
            "user_signature" => 'required',
            "supervisor_signature" => 'required',
            "items" => 'required|array|min:1',
            "items.*.p_material_id" => 'required|integer|exists:packing_materials,id',
            "items.*.lote" => 'required|string',
            "items.*.quantity" => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            "supervisor_name.required" => 'El supervisor es requerido',
            "invoice_date.required" => 'La fecha de factura es requerida',
            "user_signature.required" => 'La firma del encargado es requerida',
            "supervisor_signature.required" => 'La firma del supervisor es requerida',
            "items.required" => 'Al menos un producto debe de ser seleccionado',
            "items.array" => 'El campo productos debe ser un arreglo válido',

            "items.*.p_material_id.required" => 'El producto es obligatorio en cada ítem',
            "items.*.p_material_id.integer" => 'El ID del producto debe ser un número entero',
            "items.*.p_material_id.exists" => 'El producto seleccionado no es válido',

            "items.*.lote.required" => 'El lote es obligatorio en cada ítem',
            "items.*.lote.string" => 'El lote debe ser un texto válido',

            "items.*.quantity.required" => 'La cantidad es obligatoria en cada ítem',
            "items.*.quantity.numeric" => 'La cantidad debe ser un número',
        ];
    }
}
