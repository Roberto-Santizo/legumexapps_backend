<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyProductionDraftTasksExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $draft;

    public function __construct($draft)
    {
        $this->draft = $draft;
    }

    public function collection()
    {
        $rows = collect();

        $tasks = $this->draft->tasks;

        foreach ($tasks as $task) {
            $rows->push([
                'SKU' => $task->sku->code,
                'PRODUCTO' => $task->sku->product_name,
                'LINEA' => $task->line_id ? $task->line->name : 'SIN LINEA ASOCIADA',
                'TOTAL LIBRAS' => $task->total_lbs,
                'TOTAL CAJAS' => $task->total_lbs / $task->sku->presentation,
                'DESTINO' => $task->destination,
                'CLIENTE' => $task->sku->client_name,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['SKU', 'PRODUCTO', 'LINEA', 'TOTAL LIBRAS', 'TOTAL CAJAS', 'DESTINO', 'CLIENTE'];
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
        return 'Draft Plan Production S' . $this->draft->week;
    }
}
