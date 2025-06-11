<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserInfoResource;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payload = JWTAuth::getPayload();

        $user = [
            'id' => strval($payload->get('id')),
            'name' => $payload->get('name'),
            'email' => $payload->get('email'),
            'role' => $payload->get('role'),
        ];

        return response()->json($user);
    }

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'msg' => 'Usuario no Encontrado'
            ], 200);
        }

        return response()->json([
            'data' => new UserInfoResource($user)
        ]);
    }

    public function update(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'msg' => 'Usuario no Encontrado'
            ], 200);
        }

        try {
            $user->status = !$user->status;
            $user->save();

            return response()->json('Usuario Actualizado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Error al Actualizar el Usuario'
            ], 500);
        }
    }
}
