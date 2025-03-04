<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransportInspectionRequest extends FormRequest
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
            'planta_id' => 'required',
            'product_id' => 'required',
            'pilot_name' => 'required',
            'truck_type' => 'required',
            'plate' => 'required',
            'observations' => 'sometimes|string|nullable',
            'conditions' => 'required',
            'verify_by_signature' => 'required',
            'boletas' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'planta_id.required' => 'La planta es requerida',
            'product_id.required' => 'El producto es requerido',
            'pilot_name.required' => 'El nombre del piloto es requerido',
            'truck_type.required' => 'El tipo de camión es requerido',
            'plate.required' => 'La placa es requerida',
            'conditions.required' => 'Las condiciones son requeridas',
            'verify_by_signature.required' => 'La firma de verificación es requerida',
            'boletas.required' => 'Al menos una boleta es necesaria'
        ];
    }
}
