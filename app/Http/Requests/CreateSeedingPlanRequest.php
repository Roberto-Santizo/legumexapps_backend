<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSeedingPlanRequest extends FormRequest
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
            'file' => ['required','mimes:xlsx,csv']
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El archivo es requerido',
            'file.mimes' => 'El archivo deberia de ser de extensión XLSX o CSV'
        ];
    }
}
