<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProducerRequest;
use App\Http\Resources\ProducerResource;
use App\Models\Producer;
use Illuminate\Http\Request;

class ProducersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $producers = Producer::paginate(10);
        return ProducerResource::collection($producers);
    }

    public function GetAllProducers()
    {
        $producers = Producer::all();
        return ProducerResource::collection($producers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProducerRequest $request)
    {
        $data = $request->validated();

        try {
            Producer::create([
                'name' => $data['name'],
                'code' => $data['code']
            ]);

            return response()->json([
                'message' => 'Producer Created Successfully'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
}
