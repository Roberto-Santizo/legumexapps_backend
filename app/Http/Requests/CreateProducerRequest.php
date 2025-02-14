<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProducerRequest extends FormRequest
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
            'code' => ['required','unique:producers,code']
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'code.required' => 'El codigo es obligatorio',
            'code.unique' => 'El codigo ingresado ya existe' 
        ];
    }
}
