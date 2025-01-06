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
            'name'=> $data['name']
        ]);

        return response()->json([
            'role' => $role
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
