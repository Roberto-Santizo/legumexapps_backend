<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskWeeklyPlanRequest extends FormRequest
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
            "weekly_plan_id" => ['required'],
            "lote_id" => ['required'],
            "tarea_id" => ['required'],
            "workers_quantity" => ['required'],
            "budget" => ['required'],
            "hours" => ['required'],
            "extraordinary" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            "weekly_plan_id.required" => 'El plan semanal es obligatorio',
            "lote_id.required" => 'El lote es obligatorio',
            "tarea_id.required" => 'La tarea es obligatoria',
            "workers_quantity.required" => 'La cantidad de trabajadores es obligatoria',
            "workers_quantity.min" => 'La cantidad minima de trabajadores es 1',
            'budget.required' => 'El presupuesto es obligatoria',
            'budget.min' => 'El presupuesto minimo es 1',
            'hours.required' => 'Las horas necesarias son obligatorias',
            'hours.min' => 'Las horas minimas es 1',
            'extraordinary.required' => 'El tipo de tarea es obligatoria' 
        ];
    }
}
