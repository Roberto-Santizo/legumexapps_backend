<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInsumoRequest extends FormRequest
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
            'code' => 'required|unique:insumos,code',
            'measure' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del insumo es obligatorio',
            'code.required' => 'El codigo del insumo es obligatorio',
            'code.unique' => 'El codigo ingresado ya existe',
            'measure.required' => 'La unidad de medida es obligatoria'
        ];
    }
}
