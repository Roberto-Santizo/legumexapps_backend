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
    public function index(Request $request)
    {
        if($request->query('paginated')){
            return new PermissionCollection(Permission::paginate(10));
        }else{
            return new PermissionCollection(Permission::all());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePermissionRequest $request)
    {
        $data = $request->validated();

        try {
            Permission::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            return response()->json('Permiso Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Hubo un error al crear el permiso'
            ], 500);
        }
    }


    public function userPermissions(Request $request)
    {
        $user = $request->user();

        $permissions = $user->getAllPermissions();
        return new PermissionCollection($permissions);
    }
}
