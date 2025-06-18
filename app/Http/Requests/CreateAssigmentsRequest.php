<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssigmentsRequest extends FormRequest
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
            'data' => 'required',
            'data.*.name' => 'required',
            'data.*.code' => 'required',
            'data.*.old_position' => 'required',
            'data.*.new_position' => 'required',
            'data.*.position_id' => 'required|exists:line_positions,id'
        ];
    }
}
