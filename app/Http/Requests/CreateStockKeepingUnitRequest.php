<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStockKeepingUnitRequest extends FormRequest
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
            'code' => ['required', 'unique:stock_keeping_units,code'],
            'product_name' => ['required'],
            'client_name' => ['required'],
            'presentation' => ['sometimes'],
            'boxes_pallet' => ['sometimes'],
            'pallets_container' => ['sometimes'],
            'hours_container' => ['sometimes'],
            'recipe' => ['array', 'sometimes'],
            'recipe.*.packing_material_id' => ['required', 'exists:packing_materials,id'],
            'recipe.*.lbs_per_item' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'El código ya existe',
            'product_name.required' => 'El nombre del producto es requerido',
            'client_name.required' => 'El nombre del cliente es requerido',
        ];
    }
}
