<?php

namespace App\Exports;

use App\Models\TaskGuideline;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskGuidelinesExport implements FromCollection,  WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rows = collect();
        Carbon::setLocale('es');
        $tasks = TaskGuideline::with('task','recipe','crop','finca')->get();

        foreach ($tasks as $task) {
            $rows = $rows->push([
                'ID' => $task->id,
                'TAREA' => $task->task->name,
                'CODIGO' => $task->task->code,
                'TEMPORADA' => $task->recipe->name,
                'CULTIVO' => $task->crop->name,
                'FINCA' => $task->finca->name,
                'SEMANA' => $task->week
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['ID', 'TAREA','TEMPORADA','CODIGO', 'CULTIVO','FINCA', 'SEMANA'];
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
        return 'Manual de Tareas';
    }
}
