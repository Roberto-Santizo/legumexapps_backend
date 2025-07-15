<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PackingMaterialNecessityExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
            $items = $task->line_sku->sku->items;

            if ($items) {
                foreach ($items as $item) {
                    $quantity = $task->total_lbs / $item->lbs_per_item;
                    $rows->push([
                        'CODIGO ITEM' => $item->item->code,
                        'ITEM' => $item->item->name,
                        'SKU' => $task->line_sku->sku->code,
                        'PRODUCTO' => $task->line_sku->sku->product_name,
                        'LINEA' => $task->line_sku->line->name,
                        'CANTIDAD' => $quantity,
                        'CLIENTE' => $task->line_sku->sku->client_name,
                        'FECHA OPERACIÓN' => $task->operation_date ? $task->operation_date->format('d-m-Y') : 'SIN FECHA DE PROGRAMACIÓN',
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['CODIGO ITEM', 'ITEM', 'SKU', 'PRODUCTO', 'LINEA', 'CANTIDAD', 'CLIENTE', 'FECHA OPERACIÓN'];
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        return 'Necesidad Material Empaque S' . $this->weekly_production_plan->week;
    }
}
