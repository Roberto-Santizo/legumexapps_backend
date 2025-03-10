<?php

namespace App\Http\Controllers;

use App\Models\BiometricEmployee;

class BiometricAdminController extends Controller
{
    public function index()
    {
        $employees = BiometricEmployee::paginate(10);
        dd($employees);
    }
}
