<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLineStockKeepingUnitRequest extends FormRequest
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
            'sku_id' => [
                'required',
                Rule::unique('line_stock_keeping_units')->where(function ($query) {
                    return $query->where('line_id', $this->line_id);
                })
            ],
            'line_id' => 'required|exists:lines,id',
            'lbs_performance' => 'sometimes',
            'accepted_percentage' => 'required|numeric',
            'payment_method' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'sku_id.unique' => 'El registro de esta linea con el sku especificado ya existe',
            'sku_id.required' => 'El SKU es requerido',
            'line_id.required' => 'La linea es requerida',
            'client_id.required' => 'El cliente es requerido',
            'accepted_percentage.required' => 'El porcentaje aceptado es requerido',
            'accepted_percentage.numeric' => 'El porcentaje aceptado debe ser un valor númerico',
            'payment_method' => 'El método de pago es requerido'
        ];
    }
}
