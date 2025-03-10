<?php

namespace App\Http\Controllers;

use App\Http\Resources\QualityStatusResource;
use App\Models\QualityStatus;
use Illuminate\Http\Request;

class QualityStatusesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = QualityStatus::all();

        return QualityStatusResource::collection($statuses);
    }
}
