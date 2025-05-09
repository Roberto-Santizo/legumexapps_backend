<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInsumosReceptionRequest extends FormRequest
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
            "supplier_id" => 'required|exists:supplier_packing_materials,id',
            "invoice" => 'required',
            "received_date" => 'required',
            "invoice_date" => 'required',
            "items" => 'required|array',
            "items.*.insumo_id" => 'required|exists:insumos,id',
            "items.*.units" => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            "supplier_id.required" => 'El proveedor es obligatorio',
            "supplier_id.exists" => 'El proveedor seleccionado no existe',
            "invoice.required" => 'El número de factura es obligatoria',
            "received_date.required" => 'La fecha de recepción es requerida',
            "items.required" => 'Debe seleccionar al menos un item',
            "items.*.insumo_id.required" => 'El insumo es requerido',
            "items.*.units.required" => 'La cantidad del insumo es requerido',
            
        ];
    }
}
