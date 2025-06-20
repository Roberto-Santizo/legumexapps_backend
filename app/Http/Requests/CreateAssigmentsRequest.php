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
            'data' => 'nullable|array',
            'data.*.name' => 'required_with:data.*.code,data.*.old_position,data.*.new_position,data.*.position_id',
            'data.*.code' => 'required_with:data.*.name',
            'data.*.old_position' => 'required_with:data.*.name',
            'data.*.new_position' => 'required_with:data.*.name',
            'data.*.position_id' => 'required_with:data.*.name|exists:line_positions,id',
        ];
    }
}
