<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRules;

class CreateUserRequest extends FormRequest
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
            "name" => ['required','string'],
            "email" => ['nullable'],
            "username" => ['required','string','unique:users,username'],
            "password" => [
                'required',
                'string',
                PasswordRules::min(5)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
            "permissions" => ['required'],
            "roles" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'El nombre de usuario ya existe',
            'password' => 'La contraseña no es válida',
            'permissions' => 'Debe de asignar por lo menos un permiso',
            'roles' => 'Debe de tener por lo menos un rol'
        ];
    }
}
