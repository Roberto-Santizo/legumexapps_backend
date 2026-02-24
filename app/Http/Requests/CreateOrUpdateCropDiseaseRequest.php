<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateCropDiseaseRequest extends FormRequest
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
            'name' => ['required'],
            'crop_id' => ['required', 'exists:crops,id'],
            'week' => ['required', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'crop_id.required' => 'El cultivo es requerido',
            'crop_id.exists' => 'El cultivo no existe',
            'week.required' => 'La semama es requerida',
            'week.numeric' => 'La semana debe de ser un dato númerico'
        ];
    }
}
