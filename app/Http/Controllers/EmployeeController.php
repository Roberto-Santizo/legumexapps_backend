<?php

namespace App\Http\Controllers;

use App\Http\Resources\BiometricEmployeeResource;
use App\Http\Resources\EmployeeCollection;
use App\Models\Finca;
use App\Models\TaskWeeklyPlan;
use App\Models\WeeklyAssignmentEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id, string $taskId)
    {
        $finca = Finca::find($id);
        $task = TaskWeeklyPlan::find($taskId);
        $assigment_employees = WeeklyAssignmentEmployee::where('weekly_plan_id', $task->plan->id)->get();
        $date = Carbon::now()->format('Y-m-d');

        if (!$task) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Tarea no Encontrada'
            ], 404);
        }


        if (!$finca) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Finca no Encontrada'
            ], 404);
        }


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

        $filter_response = $response->filter(function ($employee) use ($assigment_employees, $task) {
            $has_assignments = $assigment_employees->where('code', $employee['code'])->first();

            if (!$has_assignments) {
                return $employee;
            }else {
                $exists = $assigment_employees->where('code', $employee['code'])->where('lote_id', $task->lotePlantationControl->lote_id)->first();

                if($exists){
                    return $employee;
                }
            }
        });

        return new EmployeeCollection($filter_response);
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
