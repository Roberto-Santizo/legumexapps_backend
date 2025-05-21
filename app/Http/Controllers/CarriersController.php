<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarriersResource;
use App\Models\Carrier;
use Illuminate\Http\Request;

class CarriersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('paginated')) {
            $carriers = Carrier::paginate(10);
        } else {
            $carriers = Carrier::all();
        }
        return CarriersResource::collection($carriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:carriers,code'
        ]);

        try {
            Carrier::create($data);

            return response()->json([
                'msg' => 'Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $carrier = Carrier::find($id);

        if (!$carrier) {
            return response()->json([
                'msg' => 'Carrier Not Found'
            ], 404);
        }

        return new CarriersResource($carrier);
    }
}
