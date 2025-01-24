<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Resources\PermissionCollection;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PermissionCollection(Permission::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePermissionRequest $request)
    {
        $data = $request->validated();
        
        $permiso = Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web'
        ]);

        return response()->json([
            'permiso' => $permiso
        ]);
    }

    
    public function userPermissions(Request $request)
    {
        $user = $request->user();

        return new PermissionCollection($user->getAllPermissions());
    }
}
