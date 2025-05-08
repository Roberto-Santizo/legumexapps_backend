<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackingMaterialRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'code' => 'required|unique:packing_materials,code',
            'supplier_id' => 'required|exists:supplier_packing_materials,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del item es requerido',
            'description.required' => 'La descripción del item es requerido',
            'code.required' => 'El código del item es requerido',
            'code.unique' => 'El código ingresado ya existe',
            'supplier_id.required' => "El proveedor es requerido",
            'supplier_id.exists' => "El proveedor no existe"
        ];
    }
}
