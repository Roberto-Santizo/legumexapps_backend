<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function store(LoginRequest $request)
    {
        $data = $request->validate([
            'username' => ['required', 'exists:users,username'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($data)) {
            return response()->json([
                'errors' => ['Credenciales Incorrectas']
            ], 422);
        }

        $user = Auth::user()->load(['roles:id,name', 'permissions:id,name']); 

        $role = $user->getRoleNames()->first();
        return response()->json([
            'token' => $user->createToken('token')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'status' => $user->status,
                'roles' => $role,
                'permissions' => $user->permissions->map(function($permission){
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }),
            ]
        ]);
    }


    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return [
            'user' => null
        ];
    }
}
