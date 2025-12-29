<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoteRequest extends FormRequest
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
            'name' => ['required', 'unique:lotes,name'],
            'finca_id' => ['required'],
            'size' => ['required', 'numeric'],
            'total_plants' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del lote es obligatorio',
            'name.unique' => 'El lote ya existe',
            'finca_id.required' => 'La finca relacionada es obligatoria',
            'size.required' => 'El tamaño del lote es requerido',
            'size.numeric' => 'El tamaño del lote debe de ser un dato númerico',
            'total_plants.required' => 'El total de plantas del lote es requerido',
            'total_plants.numeric' => 'El total de plantas del lote debe de ser un dato númerico',
        ];
    }
}
