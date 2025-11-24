<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCdpDatesRequest extends FormRequest
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
            'start_date' => ['required','string'],
            'end_date' => ['required','string']
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'La fecha de inicio es requerida',
            'end_date.required' => 'La fecha final es requerida' 
        ];
    }
}
