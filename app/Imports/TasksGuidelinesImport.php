<?php

namespace App\Imports;

use App\Models\Crop;
use App\Models\Finca;
use App\Models\Recipe;
use App\Models\Tarea;
use App\Models\TaskGuideline;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TasksGuidelinesImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public $tasks;
    public $recipes;
    public $crops;
    public $fincas;

    public function __construct()
    {
        $this->tasks = Tarea::all();
        $this->recipes = Recipe::all();
        $this->crops = Crop::all();
        $this->fincas = Finca::all();
    }

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {
                $task = $this->getTask($row['tarea']);
                $recipe = $this->getRecipe($row['receta']);
                $crop = $this->getCrop($row['cultivo']);
                $finca = $this->getFinca($row['finca']);

                TaskGuideline::create([
                    "task_id" => $task->id,
                    "recipe_id" => $recipe->id,
                    "crop_id" => $crop->id,
                    "finca_id" => $finca->id,
                    "week" => $row['semana'],
                    "hours" => $row['horas'],
                ]);
            }
        } catch (HttpException $th) {
            throw new HttpException(500, $th->getMessage());
        }
    }

    public function getTask(string $code)
    {
        $task = $this->tasks->where('code', $code)->first();
        if (!$task) {
            throw new HttpException(404, "Tarea {$code} no encotrada");
        }
        return $task;
    }

    public function getRecipe(string $code)
    {
        $recipe = $this->recipes->where('name', $code)->first();
        if (!$recipe) {
            throw new HttpException(404, "Receta {$code} no encotrada");
        }
        return $recipe;
    }

    public function getCrop(string $code)
    {
        $crop = $this->crops->where('code', $code)->first();
        if (!$crop) {
            throw new HttpException(404, "Cultivo {$code} no encotrada");
        }
        return $crop;
    }

    public function getFinca(string $code)
    {
        $finca = $this->fincas->where('code', $code)->first();
        if (!$finca) {
            throw new HttpException(404, "Finca {$code} no encotrada");
        }
        return $finca;
    }
}
