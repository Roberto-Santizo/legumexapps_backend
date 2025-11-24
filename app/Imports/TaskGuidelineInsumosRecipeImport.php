<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\TaskGuideline;
use App\Models\TaskInsumoRecipe;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskGuidelineInsumosRecipeImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $taskguidelines;
    public $insumos;

    public function __construct()
    {
        $this->taskguidelines = TaskGuideline::all();
        $this->insumos = Insumo::all();
    }

    public function collection(Collection $rows)
    {
        $grouped = $rows->groupBy('id');

        try {
            foreach ($grouped as $key => $rows) {
                $taskguideline = $this->taskguidelines->firstWhere('id', $key);

                if (!$taskguideline) {
                    throw new HttpException(404, "Guía de tarea {$key} no encontrada");
                }

                foreach ($rows as $row) {
                    $insumo = $this->insumos->firstWhere('code', $row['insumo']);
                    if (!$insumo) {
                        throw new HttpException(404, "Insumo {$row['insumo']} no encotrado");
                    }

                    TaskInsumoRecipe::create([
                        "task_guideline_id" => $taskguideline->id,
                        "insumo_id" => $insumo->id,
                        "quantity" => $row['cantidad'],
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw new HttpException(500, $th->getMessage());
        }
    }
}
