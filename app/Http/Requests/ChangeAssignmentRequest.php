<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeAssignmentRequest extends FormRequest
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
            "assignment_id" =>  ['required'],
            "new_name"=> ['required'],
            "new_code"=> ['required'],
            "new_position"=> ['required']
        ];
    }

    public function messages(): array
    {
        return [
            "assignment_id.required" =>  "El ID de la asignación es obligatorio",
            "new_name"=> "El nuevo nombre es obligatorio",
            "new_code"=> "El nuevo codigo es obligatorio",
            "new_position"=> "La nueva posición es obligatoria" 
        ];
    }
}
