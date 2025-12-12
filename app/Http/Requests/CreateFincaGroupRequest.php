<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFincaGroupRequest extends FormRequest
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
            'lote_id' => ['required', 'numeric', 'exists:lotes,id'],
            'finca_id' => ['required', 'numeric', 'exists:fincas,id'],
            'code' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'lote_id.required' => 'El lote es requerido',
            'lote_id.numeric' => 'El lote debe de ser un dato númerico',
            'lote_id.exists' => 'El lote no existe',
            'finca_id.required' => 'La finca es requerida',
            'finca_id.numeric' => 'La finca debe de ser un dato númerico',
            'finca_id.exists' => 'La finca no existe',
            'code.required' => 'El código es requerido',
        ];
    }
}
