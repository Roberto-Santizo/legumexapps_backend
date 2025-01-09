<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTareaRequest extends FormRequest
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
            'name' => ['required'],
            'code' => ['required','unique:tareas,code'],
            'description' => ['max:125']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'El código de la tarea ya existe',
            'description.max' => 'La descripción no puede ser mayor a 125 caracteres',
        ];
    }
}
