<?php

namespace App\Http\Controllers;

use App\Http\Resources\BiometricEmployeeResource;
use App\Http\Resources\EmployeeCollection;
use App\Models\Finca;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $finca = Finca::find($id);
        $date = Carbon::now()->format('Y-m-d');
        if ($finca->id === 2) {
            $url = env('BIOMETRICO_URL') . "/transactions/1008";
            $url2 = env('BIOMETRICO_URL') . "/transactions/1009";

            $chunck1 = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url, ['start_date' => $date, 'end_date' => $date]);
            $chunck2 = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url2, ['start_date' => $date, 'end_date' => $date]);

            $response = collect();
            $response->push($chunck1->collect());
            $response->push($chunck2->collect());
            $response = $response->flatten(1);
        } else {
            $url = env('BIOMETRICO_URL') . "/transactions/{$finca->terminal_id}";
            $response = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url, ['start_date' => $date, 'end_date' => $date])->collect();
        }

        return new EmployeeCollection($response);
    }

    public function getComodines()
    {
        $date = Carbon::now()->format('Y-m-d');
        $url = env('BIOMETRICO_URL') . "/comodines?date={$date}";
        $response = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url);

        $data = $response->collect()->map(function ($employee, $index) {
            $employee['temp_id'] = $index;
            $index += 10;
            return $employee;
        });

        return BiometricEmployeeResource::collection($data);
    }
}
