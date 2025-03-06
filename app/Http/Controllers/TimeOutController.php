<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeoutResource;
use App\Models\Timeout;
use Illuminate\Http\Request;

class TimeOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeouts = Timeout::paginate(10);
        return TimeoutResource::collection($timeouts);
    }

    public function GetAllTimeouts()
    {
        $timeouts = Timeout::all();
        return TimeoutResource::collection($timeouts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'hours' => 'required'
        ]);

        try {
            Timeout::create([
                'name' => $data['name'],
                'hours' => $data['hours'],
            ]);

            return response()->json([
                'msg' => 'Timeout Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'hours' => 'required'
        ]);

        $timeout = Timeout::find($id);

        if (!$timeout) {
            return response()->json([
                'msg' => 'Timeout not found'
            ], 404);
        }

        try {
            $timeout->update($data);
            $timeout->save();

            return response()->json([
                'msg' => 'Updated Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
