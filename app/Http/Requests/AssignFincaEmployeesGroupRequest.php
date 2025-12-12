<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignFincaEmployeesGroupRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'data.*.assign_id' => ['required', 'exists:weekly_assignment_employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'La información es requerida',
            'data.array' => 'La información debe de ser un arreglo',
            'data.*.assign_id.required' => 'El id de la asignación es requerido',
            'data.*.assign_id.exists' => 'La asignación no existe',
        ];
    }
}
