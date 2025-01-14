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
            'name' => ['required','unique:lotes,name'],
            'finca_id' => ['required'],
            'cdp_id'=> ['required']
        ];
    }
    
    public function messages() : array
    {
        return [
            'name.required' => 'El nombre del lote es obligatorio',
            'name.unique' => 'El lote ya existe',
            'finca_id.required' => 'La finca relacionada es obligatoria',
            'cdp_id.required' => 'Seleccione el CDP activo relacionado al lote'
        ];
    }
}
