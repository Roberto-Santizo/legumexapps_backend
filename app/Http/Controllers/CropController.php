<?php

namespace App\Http\Controllers;

use App\Http\Resources\CropCollection;
use App\Models\Crop;
use Illuminate\Http\Request;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CropCollection(Crop::all());
    }
}
