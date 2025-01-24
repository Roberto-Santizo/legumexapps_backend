<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthUserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        return new AuthUserResource($user);
    }
}
