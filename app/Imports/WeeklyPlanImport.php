<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WeeklyPlanImport implements WithMultipleSheets
{
    private $tareasMap = [];
  
    public function sheets() : array 
    {
        return [
            0 => new WeeklyTasksImport($this->tareasMap),
            1 => new WeeklyInsumosTasksImport($this->tareasMap)
        ];
    }
}
