<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserInfoResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    public function index()
    {
        return new UserCollection(User::with('roles')->with('permissions')->get());
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();

        try {
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
            return  response()->json('Usuario Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Existe un error al crear el usuario'
            ], 500);
        }
    }

    public function show(User $user)
    {
        $user->with(['roles']);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $datos = $request->validated();

        if (!$request->filled('password')) {
            $datos['password'] = $user->password;
        } else {
            $datos['password'] = bcrypt($datos['password']);
        }

        try {
            $user->removeRole($user->roles->first());
            $user->revokePermissionTo($user->permissions);

            $user->update($datos);
            $user->assignRole($datos['roles']);

            foreach ($datos['permissions'] as $permission_id) {
                $permission = Permission::find($permission_id);
                $user->givePermissionTo($permission);
            }

            return response()->json('Usuario Actualizado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function updateStatus(User $user)
    {
        $user->status = ($user->status === 0) ? 1 : 0;
        $user->save();

        return new UserCollection(User::with('roles')->with('permissions')->get());
    }

    public function UsersInfo(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'data' => new UserInfoResource($user)
        ]);
    }
}
