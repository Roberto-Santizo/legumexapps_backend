<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InsumosExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $weekly_plan;

    public function __construct($weekly_plan)
    {
        $this->weekly_plan = $weekly_plan;
    }

    public function collection()
    {
        $rows = collect();
        Carbon::setLocale('es');
        $tasks = $this->weekly_plan->tasks;

        foreach ($tasks as $task) {
            if ($task->end_date && $task->insumos->count() > 0) {
                foreach ($task->insumos as $insumo) {
                    $rows->push([
                        'TAREA' => $task->task->name,
                        'CDP' => $task->lotePlantationControl->cdp->name,
                        'INSUMO' => $insumo->insumo->name,
                        'CODIGO' => $insumo->insumo->code,
                        'UNIDAD DE MEDIDA' => $insumo->insumo->measure,
                        'CANTIDAD ASIGNADA' => $insumo->assigned_quantity,
                        'CANTIDAD UTILIZADA' => $insumo->used_quantity,
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['TAREA', 'CDP', 'INSUMO', 'CODIGO', 'UNIDAD DE MEDIDA', 'CANTIDAD ASIGNADA', 'CANTIDAD UTILIZADA'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
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
        return 'Reporte De Insumos';
    }
}
