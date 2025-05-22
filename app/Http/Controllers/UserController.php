<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserInfoResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json($user->getRoleNames()->first(), 200);
    }

    public function show(Request $request, string $id)
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

    public function update(Request $request, string $id)
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
