<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyProductionExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $weekly_production_plan;

    public function __construct($weekly_production_plan)
    {
        $this->weekly_production_plan = $weekly_production_plan;
    }

    public function collection()
    {
        $rows = collect();

        $tasks = $this->weekly_production_plan->tasks;

        foreach ($tasks as $task) {
            $boxes = $task->line_sku->sku->presentation ? $task->total_lbs / $task->line_sku->sku->presentation : '';
            $pallets = $task->line_sku->sku->boxes_pallet ? ($boxes / $task->line_sku->sku->boxes_pallet) : '';
            $rows->push([
                'SKU' => $task->line_sku->sku->code,
                'PRODUCTO' => $task->line_sku->sku->product_name,
                'LINEA' => $task->line_sku->line->name,
                'TOTAL LIBRAS' => $task->total_lbs,
                'TOTAL CAJAS' => $boxes,
                'TOTAL TARIMAS' => $pallets,
                'FECHA OPERACIÓN' => $task->operation_date ? $task->operation_date->format('d-m-Y') : 'SIN PROGRAMACIÓN',
            ]);
        }

        return $rows;
    }
    public function headings(): array
    {
        return ['SKU', 'PRODUCTO', 'LINEA', 'TOTAL LIBRAS', 'TOTAL CAJAS', 'TOTAL TARIMAS', 'FECHA OPERACIÓN'];
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
        return 'Plan Producción S' . $this->weekly_production_plan->week;
    }
}
