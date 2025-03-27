<?php

namespace App\Exports;

use App\Models\BiometricEmployee;
use App\Models\BiometricTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use function PHPUnit\Framework\isEmpty;

class PlanillaProductionExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        $rows = collect();
        Carbon::setLocale('es');

        try {
            foreach ($this->tasks as $task) {
                $total_hours = ($task->finished_tarimas / 2);
                $day = $task->end_date ? $task->start_date->isoFormat('dddd') : '';
                $total_employees = $task->employees->filter(function($employee) use($task){
                    $entrance = BiometricTransaction::where('pin',$employee->position)->whereDate('event_time',$task->start_date)->get()->first();
                    if($entrance){
                        return $employee;
                    }
                });

                foreach ($task->employees as $employee) {
                    $exists = $total_employees->where('position',$employee->position)->first();
                    if($exists){
                        $rows->push([
                            'POSICION' => $employee->position,
                            'CODIGO' => $employee->code,
                            'NOMBRE' => $employee->name,
                            'HORAS' =>  $total_hours / $total_employees->count(),
                            'DIA' => $day
                        ]);
                    } else{
                        $rows->push([
                            'POSICION' => $employee->position,
                            'CODIGO' => $employee->code,
                            'NOMBRE' => $employee->name,
                            'HORAS' =>  '',
                            'DIA' => $day
                        ]);
                    }
                  
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['POSICION', 'CODIGO', 'NOMBRE', 'HORAS', 'DIA'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
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
        return 'PLANILLA DE HORAS';
    }
}
