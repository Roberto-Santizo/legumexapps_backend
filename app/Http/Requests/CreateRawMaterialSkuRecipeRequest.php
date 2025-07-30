<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRawMaterialSkuRecipeRequest extends FormRequest
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
            'raw_material_item_id' => ['required', 'exists:raw_material_items,id'],
            'percentage' => ['required']
        ];
    }
    public function messages(): array
    {
        return [
            'raw_material_item_id.required' => 'El item de materia prima es requerido',
            'raw_material_item_id.exists' => 'El item no existe',
            'percentage.required' => 'El porcentaje es requerido',
        ];
    }
}
