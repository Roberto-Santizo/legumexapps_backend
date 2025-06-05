<?php

namespace App\Exports;

use App\Models\BiometricTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlanillaProductionExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMultipleSheets
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
        Carbon::setLocale('es');

        $positions = $this->tasks->load('employees')->pluck('employees')->flatten(1)->unique('code')->values();

        $positions->map(function ($position) use ($rows) {
            $position['hours'] = 0;
            try {
                foreach ($this->tasks as $task) {
                    $exists = $task->employees()->where('code', $position->code)->exists();
                    $total_hours = 0;

                    if ($exists) {
                        if ($task->line_sku->payment_method) {
                            $total_hours = $task->start_date->diffInHours($task->end_date);
                        } else {
                            $total_hours = $task->total_lbs_produced / $task->line_sku->lbs_performance;
                        }
                        $position->hours += $total_hours;
                    }
                }

                $rows->push([
                    'POSICION' => $position->position,
                    'CODIGO' => $position->code,
                    'NOMBRE' => $position->name,
                    'HORAS' => $position->hours,
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        });

        return $rows;
    }

    public function headings(): array
    {
        return ['POSICION', 'CODIGO', 'NOMBRE', 'HORAS'];
    }

    public function sheets(): array
    {
        return [
            new PlanillaProductionExport($this->tasks, $this->line),
            new PlanillaProductionDetailsExport($this->tasks,$this->line)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
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
        return 'PLANILLA';
    }
}
