<?php

namespace App\Exports;

use App\Models\EmployeePaymentWeeklySummary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class FincaPlanillaExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $weekly_plan;
    protected $septimo = ((3097.21 * 12) / 365) * 1.5;
    protected $bono =  ((250 * 12) / 365) * 7;

    public function __construct($weekly_plan)
    {
        $this->weekly_plan = $weekly_plan;
    }

    public function collection()
    {
        $rows = collect();
        $employees = EmployeePaymentWeeklySummary::where('weekly_plan_id',$this->weekly_plan->id)->get()->groupBy('code');

        foreach ($employees as $key => $tasks) {
            $hours = $tasks->sum('hours');
            $amount = $tasks->sum('amount');

            $rows->push([
                'CODIGO' => $key,
                'HORAS SEMANALES' => $hours,
                'MONTO' => $amount,
                'SEPTIMO' => $hours > 44 ? $this->septimo : 0,
                'BONIFICACIÓN' =>  $hours > 44 ? $this->bono : 0,
                'TOTAL A DEVENGAR' => $hours > 44 ? ($amount + $this->septimo + $this->bono) : $amount
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['CODIGO', 'HORAS SEMANALES', 'MONTO', 'SEPTIMO', 'BONIFICACIÓN', 'TOTAL A DENVEGAR'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '5564eb']],
        ]);
    }

    public function title(): string
    {
        return 'Detalle Tareas';
    }
}
