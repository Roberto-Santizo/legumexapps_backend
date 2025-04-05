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
            "budget" => ['required','min:1'],
            "end_date" => ['nullable'],
            "end_time" => ['nullable'],
            "hours" => ['required'],
            "start_date"=> ['nullable'],
            "start_time" => ['nullable'],
            "weekly_plan_id" => ['required','string','exists:weekly_plans,id'],
            "slots" => ['required']
        ];
    }
}
