<?php

namespace App\Exports;

use App\Models\EmployeePaymentWeeklySummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeTaskDetailExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $weekly_plan;
    protected $entries;

    public function __construct($weekly_plan)
    {
        $this->weekly_plan = $weekly_plan;
        $startOfWeek = Carbon::now()->setISODate($weekly_plan->year, $weekly_plan->week)->startOfWeek();
        $endOfWeek = Carbon::now()->setISODate($weekly_plan->year, $weekly_plan->week)->endOfWeek();
        $url = env('BIOMETRICO_URL') . "/transactions/{$weekly_plan->finca->terminal_id}";
        $entries = Http::withHeaders(['Authorization' => env('BIOMETRICO_APP_KEY')])->get($url, ['start_date' => $startOfWeek->format('Y-m-d'), 'end_date' => $endOfWeek->format('Y-m-d')]);
        $this->entries = $entries->collect();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        Carbon::setLocale('es');
        $weekly_plan = $this->weekly_plan;

        return EmployeePaymentWeeklySummary::where('weekly_plan_id', $weekly_plan->id)->with(['plan', 'task', 'assigment'])->get()->map(function ($assignment) use ($weekly_plan) {
            $taskData = $assignment->task_weekly_plan_id
                ? $assignment->task
                : $assignment->assigment;

            $biometric_data = $this->getEmployeeRegistration($assignment->code, $assignment->date);

            $lote = $assignment->task_weekly_plan_id
                ? $taskData->lotePlantationControl->lote->name
                : $taskData->TaskCropWeeklyPlan->lotePlantationControl->lote->name;

            $taskName = $assignment->task_weekly_plan_id
                ? $taskData->task->name
                : $taskData->TaskCropWeeklyPlan->task->name;


            return [
                'CODIGO' => $assignment->code,
                'EMPLEADO' => $assignment->name,
                'LOTE' => $lote,
                'TAREA REALIZADA' => $taskName,
                'PLAN' => $weekly_plan->week,
                'MONTO GANADO' => $assignment->amount,
                'HORAS REALES' => $assignment->hours,
                'HORAS TEORICAS' => $assignment->theorical_hours,
                'HORAS BIOMETRICO' => 'HORAS BIOMETRICO',
                'ENTRADA BIOMETRICO' => $biometric_data['entrance'],
                'SALIDA BIOMETRICO' => $biometric_data['exit'],
                'DIA' => $assignment->date->translatedFormat('l'),
            ];
        });
    }


    public function getEmployeeRegistration($code, $date)
    {
        $employee = $this->entries->firstWhere('code', $code);

        if (!$employee || empty($employee['transactions'])) {
            return [
                'entrance' => '',
                'exit'     => '',
            ];
        }

        $records = collect($employee['transactions'])
            ->filter(function ($transaction) use ($date) {
                $punch_time = Carbon::parse($transaction['punch_time'])->format('Y-m-d');
                $date = Carbon::parse($date);
                return $punch_time === $date->format('Y-m-d');
            })
            ->sortBy('punch_time')
            ->values();

        $firstRecord = $records->first();
        $lastRecord = $records->last();
        return [
            'entrance' => $firstRecord
                ? Carbon::parse($firstRecord['punch_time'])
                    ->timezone('America/Guatemala')
                    ->format('d-m-Y h:i:s A')
                : '',
            'exit' => $lastRecord
                ? Carbon::parse($lastRecord['punch_time'])
            ->timezone('America/Guatemala')
            ->format('d-m-Y h:i:s A')
                : '',
        ];
    }


    public function headings(): array
    {
        return ['CODIGO', 'EMPLEADO', 'LOTE', 'TAREA REALIZADA', 'PLAN', 'MONTO GANADO', 'HORAS REALES', 'HORAS TEORICAS', 'HORAS BIOMETRICO', 'ENTRADA BIOMETRICO', 'SALIDA BIOMETRICO', 'DIA'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '5564eb']],
        ]);
    }

    public function title(): string
    {
        return 'Detalle Tareas';
    }
}
