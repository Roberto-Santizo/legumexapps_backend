<?php

namespace App\Http\Controllers;

use App\Http\Resources\TareaCropResource;
use App\Models\TaskCrop;

class TaskCropController extends Controller
{
    public function GetAllTasksCrop()
    {
        return TareaCropResource::collection(TaskCrop::all());
    }
}
