<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateCropDiseaseSymptomRequest extends FormRequest
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
            'symptom' =>            ['required'],
            'crop_disease_id' =>    ['required', 'exists:crop_diseases,id'],
            'crop_part_id' =>       ['required', 'exists:crop_parts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'symptom.required' =>           'La descripción del sintoma es requerido',
            'crop_disease_id.required' =>   'La enfermedad es requerida',
            'crop_disease_id.exists' =>     'La enfermedad no existe',
            'crop_part_id.required' =>      'La parte de la planta es requerida',
            'crop_part_id.exists' =>        'La parte de la planta no existe'
        ];
    }
}
