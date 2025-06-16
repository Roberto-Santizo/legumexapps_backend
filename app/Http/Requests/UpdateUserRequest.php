<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRules;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        return [
            "name" => ['required','string'],
            "email" => ['sometimes'],
            "username" => ['required','string','unique:users,username,'.$userId],
            "password" => [
                'nullable',
                'string',
                PasswordRules::min(5)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
            "permissions" => ['required'],
            "role" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'El nombre de usuario ya existe',
            'password' => 'La contraseña no es válida',
            'email.unique' => 'El correo electrónico ya existe',
            'permissions' => 'Debe de asignar por lo menos un permiso',
            'roles' => 'Debe de tener por lo menos un rol'
        ];
    }
}
