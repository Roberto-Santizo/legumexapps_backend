<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest as RequestsCreateRoleRequest;
use App\Http\Resources\CreateRoleRequest;
use App\Http\Resources\RoleCollection;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new RoleCollection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsCreateRoleRequest $request)
    {
        $data = $request->validated();

        $role = Role::create([
            'name'=> $data['name'],
            'guard_name' => 'web'
        ]);

        return response()->json([
            'role' => $role
        ]);
    }

    public function userRoles(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'name'=> $user->getRoleNames()->first()
        ]);
    }
}
