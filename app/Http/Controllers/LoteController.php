<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoteChecklistRequest;
use App\Http\Requests\CreateLoteRequest;
use App\Http\Resources\LoteCollection;
use App\Http\Resources\LotePlantationControlResource;
use App\Imports\UpdateLotesImport;
use App\Models\Lote;
use App\Services\LoteService;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lote::query();

        if ($request->query('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->query('finca_id')) {
            $query->where('finca_id', $request->query('finca_id'));
        }

        if ($request->query('cdp')) {
            $query->whereHas('cdp', function ($query) use ($request) {
                $query->whereHas('cdp', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->query('cdp') . '%');
                });
            });
        }

        $query->with('cdp');

        if ($request->query('paginated')) {
            return new LoteCollection($query->paginate(10));
        } else {
            // dd($query->get());
            return new LoteCollection($query->get());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLoteRequest $request)
    {
        $data = $request->validated();

        try {
            Lote::create([
                'name' => $data['name'],
                'finca_id' => $data['finca_id'],
                'size' => $data['size'],
                'total_plants' => $data['total_plants']
            ]);

            return response()->json('Lote Creado Correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 500,
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lote = Lote::find($id);
        return response()->json([
            'data' => LotePlantationControlResource::collection($lote->lote_cdps)
        ]);
    }

    public function UpdateLotes(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new UpdateLotesImport, $request->file('file'));

            return response()->json('Lotes Actualizados Correctamente', 200);
        } catch (Exception $th) {
            return response()->json([
                'errors' => 'Hubo un error al actualizar los lotes'
            ], 500);
        }
    }

    public function createChecklist(CreateLoteChecklistRequest $request, string $id)
    {
        try {
            $data = $request->validated()['data'];
            $service = new LoteService();
            $JwtPayload = JWTAuth::getPayload();
            $userId = $JwtPayload->get('id');

            $service->createLoteChecklist($userId, $id, $data);
            
            return response()->json([
                'statusCode' => 201,
                'message' => 'Checklist creado correctamente'
            ], 201);
        } catch (HttpException $th) {
            return response()->json([
                'statusCode' => $th->getStatusCode(),
                'message' => $th->getMessage()
            ], $th->getStatusCode());
        }
    }
}
