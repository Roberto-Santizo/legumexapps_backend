<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinesResource;
use App\Http\Resources\LinesSelectResource;
use App\Models\BitacoraLines;
use App\Models\Line;
use Illuminate\Http\Request;

class LinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lines = Line::select('id', 'code', 'total_persons', 'name')->paginate(10);

        return LinesResource::collection($lines);
    }

    public function GetAllLines()
    {
        $lines = Line::select('id', 'code', 'total_persons', 'shift', 'name')->get();

        return LinesSelectResource::collection($lines);
    }

    public function GetAllLinesBySku(string $id)
    {
        $lines = Line::select('id', 'code', 'total_persons', 'shift', 'name')
            ->whereHas('skus', function ($query) use ($id) {
                $query->where('sku_id', $id);
            })
            ->with(['skus' => function ($query) use ($id) {
                $query->where('sku_id', $id);
            }])
            ->get();

        return LinesSelectResource::collection($lines);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:lines,code',
            'total_persons' => 'required',
            'shift' => 'required',
            'name' => 'required'
        ]);

        try {
            Line::create($data);

            return response()->json('Linea Creada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $line = Line::find($id);
        if (!$line) {
            return response()->json([
                'msg' => 'Line not Found'
            ], 404);
        }

        return new LinesResource($line);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            "code" => "required|unique:lines,code,{$id}",
            "total_persons" => "required",
        ]);


        $line = Line::find($id);

        if (!$line) {
            return response()->json([
                'msg' => 'Line Not Found'
            ], 404);
        }

        try {
            if ($line->code != $data['code'] || $line->total_persons != $data['total_persons']) {
                BitacoraLines::create([
                    'line_id' => $line->id,
                    'old_code' => $line->code,
                    'new_code' => $data['code'],
                    'old_total_persons' => $line->total_persons,
                    'new_total_persons' => $data['total_persons']
                ]);
            }

            $line->code = $data['code'];
            $line->total_persons = $data['total_persons'];
            $line->save();

            return response()->json('Linea Actualizada Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage()
            ], 500);
        }
    }
}
