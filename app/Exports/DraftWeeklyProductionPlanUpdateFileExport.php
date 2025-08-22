<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DraftWeeklyProductionPlanUpdateFileExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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

        $drafts_tasks = $this->draft->tasks;

        try {
            foreach ($drafts_tasks as $task) {
                $boxes = $task->sku->presentation ? $task->total_lbs / $task->sku->presentation : '';
                $pallets = $task->sku->boxes_pallet ? ($boxes / $task->sku->boxes_pallet) : '';
                $rows->push([
                    'ID' => $task->id,
                    'SKU' => $task->sku->code,
                    'PRODUCTO' => $task->sku->product_name,
                    'PRESENTACIÓN' => $task->sku->presentation ? $task->sku->presentation : 0,
                    'TOTAL CAJAS' => $task->sku->presentation ? $task->total_lbs / $task->sku->presentation : 0,
                    'TOTAL LIBRAS' => $task->total_lbs,
                    'PALLETS' => $pallets,
                    'DESTINO' => $task->destination,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['ID', 'SKU', 'PRODUCTO', 'PRESENTACIÓN', 'TOTAL CAJAS', 'TOTAL LIBRAS', 'PALLETS', 'DESTINO'];
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
        return 'Actualización Draft Plan Production S' . $this->draft->week;
    }
}
