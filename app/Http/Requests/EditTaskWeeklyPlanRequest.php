<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditTaskWeeklyPlanRequest extends FormRequest
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
            "weekly_plan_id" => ['required', 'exists:weekly_plans,id'],
            "finca_group_id" => ['required', 'exists:finca_groups,id'],
            "budget" => ['required', 'numeric'],
            "hours" => ['required', 'numeric'],
            "start_date" => ['sometimes'],
            "end_date" => ['sometimes'],
            "operation_date" => ['required', 'string'],
        ];
    }
}
