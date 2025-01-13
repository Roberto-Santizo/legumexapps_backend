<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCDPRequest extends FormRequest
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
            "name" => ['required','string','unique:plantation_controls,name'],
            "density" => ['required'],
            "size" => ['required'],
            "start_date" => ['required'],
            "crop_id" => ['required'],
            "recipe_id" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del CDP es obligatorio',
            'name.unique' => 'El nombre del CDP ya existe',
            'density.required' => 'La densidad del CDP es obligatorio',
            'size.required' => 'El tamaño del CDP es obligatorio',
            'start_date.required' => 'La fecha de siempre del CDP es obligatoria',
            'crop_id.required' => 'El cultivo relacionado es obligatorio',
            'recipe_id.required' => 'La receta relacionada es obligatoria'
        ];
    }
}
