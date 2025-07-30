<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRawMaterialItemRequest extends FormRequest
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
            'code' => ['sometimes','string'],
            'product_name' => ['required'],
            'type' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código del item es requerido',
            'code.unique' => 'El código del item ya existe',
            'code.string' => 'El código del item debe de ser texto',
            'product_name.required' => 'El nombre del producto es requerido',
            'type.required' => 'El tipo de producto es requerido'
        ];
    }
}
