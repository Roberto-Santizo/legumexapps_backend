<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeAssigmentsRequest extends FormRequest
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
            'previous_config' => 'required|boolean',
            'data' => 'sometimes|array',
            'data.*.old_employee' => 'required',
            'data.*.old_employee.id' => 'required|exists:task_production_employees,id',
            'data.*.old_employee.code' => 'required',
            'data.*.old_employee.name' => 'required',
            'data.*.old_employee.position' => 'required',
            'data.*.new_employee' => 'required',
            'data.*.new_employee.id' => 'required',
            'data.*.new_employee.name' => 'required',
            'data.*.new_employee.code' => 'required',
            'data.*.new_employee.position' => 'required',
        ];
    }
}
