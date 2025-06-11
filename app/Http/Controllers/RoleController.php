<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest as RequestsCreateRoleRequest;
use App\Http\Resources\RoleCollection;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            return new RoleCollection(Role::paginate(10));
        } else {
            return new RoleCollection(Role::all());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsCreateRoleRequest $request)
    {
        $data = $request->validated();

        try {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            return response()->json('Rol Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear el rol'
            ], 500);
        }
    }

    public function userRoles(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'name' => $user->getRoleNames()->first()
        ]);
    }
}
