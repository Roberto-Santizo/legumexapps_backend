<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    public function index()
    {
        return new UserCollection(User::with('roles')->with('permissions')->get());
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? '',
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'status' => 1
        ]);

        return  response()->json([
            'user' => $user,
            'message' => 'Usuario creado correctamente'
        ]);
    }
}
