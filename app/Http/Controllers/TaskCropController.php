<?php

namespace App\Http\Controllers;

use App\Http\Resources\TareaCropResource;
use App\Models\TaskCrop;

class TaskCropController extends Controller
{
    public function Index()
    {
        return TareaCropResource::collection(TaskCrop::all());
    }
}
