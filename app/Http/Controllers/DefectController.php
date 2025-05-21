<?php

namespace App\Http\Controllers;

use App\Http\Resources\DefectResource;
use App\Models\Defect;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if($request->query('paginated')){
            $defects = Defect::paginate(10);
        }else{
            $defects = Defect::all();
        }
        
        return DefectResource::collection($defects);
    }
}
