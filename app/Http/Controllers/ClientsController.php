<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientSelectResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::select('name')->paginate(10);
        return ClientResource::collection($clients);
    }

    public function GetAllClients()
    {
        $clients = Client::select('id','name')->get();
        return ClientSelectResource::collection($clients);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        try {
            Client::create($data);

            return response()->json('Cliente Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
