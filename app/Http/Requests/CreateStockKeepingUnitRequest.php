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
            'box_id' => ['required', 'exists:packing_materials,id'],
            'bag_id' => ['required', 'exists:packing_materials,id'],
            'bag_inner_id' => ['required', 'exists:packing_materials,id'],
            'presentation' => ['sometimes'],
            'boxes_pallet' => ['sometimes'],
            'config_box' => ['sometimes'],
            'config_bag' => ['sometimes'],
            'config_inner_bag' => ['sometimes'],
            'pallets_container' => ['sometimes'],
            'hours_container' => ['sometimes'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'El código ya existe',
            'product_name.required' => 'El nombre del producto es requerido',
            'client_name.required' => 'El nombre del cliente es requerido',
            'box_id.required' => 'La referencia de la caja es obligatoria',
            'box_id.exists' => 'La referencia de la caja no existe',
            'bag_id.required' => 'La referencia de la bolsa es obligatoria',
            'bag_id.exists' => 'La referencia de la bolsa no existe',
            'bag_inner_id.required' => 'La referencia de la bolsa inner es obligatoria',
            'bag_inner_id.exists' => 'La referencia de la bolsa inner no existe',
        ];
    }
}
