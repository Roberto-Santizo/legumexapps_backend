<?php

namespace App\Http\Controllers;

use App\Http\Resources\QualityStatusResource;
use App\Models\QualityStatus;

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
