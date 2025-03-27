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
                    return $query->where('line_id', $this->line_id)
                        ->where('client_id', $this->client_id);
                })
            ],
            'line_id' => 'required',
            'client_id' => 'required',
            'lbs_performance' => 'required|numeric'
        ];
    }

    public function messages(): array
    {
        return [
            'sku_id.unique' => 'El registro de esta linea con el sku especificado ya existe',
            'sku_id.required' => 'El SKU es requerido',
            'line_id.required' => 'La linea es requerida',
            'client_id.required' => 'El cliente es requerido',
            'lbs_performance.required' => 'El rendimeinto es requerido',
            'lbs_performance.numeric' => 'El dato debería de ser númerico'
        ];
    }
}
