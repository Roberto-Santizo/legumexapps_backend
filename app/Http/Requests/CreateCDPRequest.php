<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCDPRequest extends FormRequest
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
            "name" => ['required', 'string', 'unique:plantation_controls,name'],
            "start_date" => ['required'],
            "end_date" => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del CDP es obligatorio',
            'name.unique' => 'El nombre del CDP ya existe',
            'start_date.required' => 'La fecha de inicio es requerida',
            'end_date.required' => 'La fecha de cierre es requerida',
        ];
    }
}
