<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBoletaRMPRequest extends FormRequest
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
            'productor_plantation_control_id' => 'required',
            'producer_id' => 'required',
            'finca_id'=> 'required',
            'inspector_name' => 'required',
            'product_id' => 'required',
            'total_baskets' => 'required',
            'weight' => 'required',
            'basket_id' => 'required',
            'driver_signature' => 'required',
            'inspector_signature' => 'required',
            'prod_signature' => 'required',
            'carrier_id' => 'required',
            'plate_id' => 'required',
            'quality_percentage' => 'required',
        ];
    }
}
