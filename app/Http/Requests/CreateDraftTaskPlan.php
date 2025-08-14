<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDraftTaskPlan extends FormRequest
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
            'line_id' => ['exists:lines,id'],
            'stock_keeping_unit_id' => ['required', 'exists:stock_keeping_units,id'],
            'total_lbs' => ['required'],
            'destination' => ['required']
        ];
    }
}
