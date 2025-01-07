<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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
        ])->assignRole($data['roles']);

        foreach ($data['permissions'] as $permission_id) {
            $permission = Permission::find($permission_id);
            $user->givePermissionTo($permission);
        }

        return  response()->json([
            'user' => $user,
            'message' => 'Usuario creado correctamente'
        ]);
    }

    public function user(User $user)
    {
        $user->with(['roles', 'permissions']);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $datos = $request->validated();

        if (!$request->filled('password')) {
            $datos['password'] = $user->password;
        }else{
            $datos['password'] = bcrypt($datos['password']);
        }

        
        $user->removeRole($user->roles->first()); 
        $user->revokePermissionTo($user->permissions);
        
        $user->update($datos);
        $user->assignRole($datos['roles']);
        
        foreach ($datos['permissions'] as $permission_id) {
            $permission = Permission::find($permission_id);
            $user->givePermissionTo($permission);
        }
        
        $user->with(['roles', 'permissions']);
        
        return new UserResource($user);
    }
}
