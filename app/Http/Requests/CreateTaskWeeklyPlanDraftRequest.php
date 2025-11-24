<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskWeeklyPlanDraftRequest extends FormRequest
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
            'draft_weekly_plan_id' => ['required', 'exists:draft_weekly_plans,id'],
            'hours' => ['required'],
            'budget' => ['required'],
            'slots' => ['required'],
            'tags' => ['sometimes']
        ];
    }

    public function messages(): array
    {
        return [
            'draft_weekly_plan_id.required' => 'El plan semanal es requerido',
            'hours.required' => 'Las horas son requeridas',
            'budget.required' => 'El presupuesto es requerido',
            'slots.required' => 'Los cupos son requeridos',
        ];
    }
}
