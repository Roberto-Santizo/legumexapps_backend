<?php

namespace App\Exports;

use App\Models\BiometricTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlanillaProductionDetailsExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $tasks;
    protected $line;

    public function __construct($tasks, $line)
    {
        $this->tasks = $tasks;
        $this->line = $line;
    }
    public function collection()
    {
        $rows = collect();

        foreach ($this->tasks as $task) {
            try {
                $total_hours = 0;

                if ($task->line_sku->payment_method) {
                    $total_hours = $task->start_date->diffInHours($task->end_date);
                } else {
                    $total_hours = $task->total_lbs_produced / $task->line_sku->lbs_performance;
                };

                $day = $task->end_date ? $task->start_date->isoFormat('dddd') : '';
                $total_employees = $task->employees->filter(function ($employee) use ($task) {
                    $entrance = BiometricTransaction::where('last_name', $employee->position)->whereDate('event_time', $task->start_date)->get()->first();
                    if ($entrance) {
                        return $employee;
                    }
                });

                foreach ($this->line->positions as $position) {
                    $exists = $total_employees->firstWhere('position', $position->name);

                    $rows->push([
                        'POSICION' => $position->name,
                        'CODIGO' => $exists->code ?? 'VACANTE',
                        'NOMBRE' => $exists->name ?? 'VACANTE',
                        'HORAS' => $exists ? $total_hours : 'VACANTE',
                        'TRABAJO' => $exists ? ($exists->unAssigned ? 'PARCIAL' : 'TOTAL') : 'VACANTE',
                        'SKU' => $task->line_sku->sku->code,
                        'HORAS TRABAJADAS' => $exists->unAssigned->hours ?? '',
                        'RAZON' => $exists->unAssigned->taskProductionUnassign->reason ?? '',
                        'DIA' => $day
                    ]);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return $rows;
    }


    public function headings(): array
    {
        return ['POSICION', 'CODIGO', 'NOMBRE', 'HORAS', 'TRABAJO', 'SKU', 'HORAS TRABAJADAS', 'RAZON', 'DIA'];
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '5564eb'],
            ],
        ]);
    }

    public function title(): string
    {
        return 'PLANILLA DETALLES';
    }
}
