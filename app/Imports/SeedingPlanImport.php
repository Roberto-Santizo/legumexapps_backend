<?php

namespace App\Imports;

use App\Models\AnnualSalary;
use App\Models\Crop;
use App\Models\DraftWeeklyPlan;
use App\Models\Finca;
use App\Models\Lote;
use App\Models\PlantationControl;
use App\Models\Recipe;
use App\Models\TaskGuideline;
use App\Models\TaskWeeklyPlanDraft;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SeedingPlanImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public $fincas;
    public $cdps;
    public $crops;
    public $recipes;
    public $lotes;
    public $plans;
    public $tasks;

    public function __construct()
    {
        $this->fincas = Finca::all();
        $this->cdps = PlantationControl::all();
        $this->lotes = Lote::all();
        $this->crops = Crop::all();
        $this->recipes = Recipe::all();
        $this->plans = DraftWeeklyPlan::all();
        $this->tasks = TaskGuideline::all();
    }

    public function collection(Collection $rows)
    {
        $groupedRows = $rows->groupBy('finca');
        foreach ($groupedRows as $key => $rows) {
            $finca = $this->fincas->where('code', $key)->first();

            if (!$key) {
                continue;
            }

            if (!$finca) {
                throw new HttpException(404, "Finca {$finca} no encotrada");
            }

            foreach ($rows as $row) {
                $startDate = Carbon::parse(Date::excelToDateTimeObject($row['fecha_inicio']));
                $endDate = Carbon::parse(Date::excelToDateTimeObject($row['fecha_final']));
                $weeks = range($startDate->weekOfYear, $endDate->weekOfYear);

                $recipe = $this->getRecipe($row['temporada']);
                $crop = $this->getCrop($row['cultivo']);

                foreach ($weeks as $index => $week) {
                    $recipe = $this->getRecipe($row['temporada']);
                    $crop = $this->getCrop($row['cultivo']);
                    $draftWeeklyPlan = $this->getOrCreateDraftWeeklyPlan($week, $finca->id, $row['year']);
                    $lote = $this->getLote($row['lote']);
                    $cdp = $this->getCdp($row['cdp'], $lote, $startDate, $endDate, $recipe, $crop);
                    $tasks = $this->getTasks($recipe->id, $crop->id, $finca->id, $index + 1);

                    foreach ($tasks as $task) {
                        $slots = $task->hours / 8;
                        $hour = AnnualSalary::all();
                        $hours = $lote->size * $task->hours_per_size;

                        TaskWeeklyPlanDraft::create([
                            'task_guideline_id' => $task->id,
                            'hours' => $hours,
                            'budget' => $hours * $hour->last()->amount,
                            'slots' => $slots < 0 ? 1 : floor($slots),
                            'draft_weekly_plan_id' => $draftWeeklyPlan->id,
                            'plantation_control_id' => $cdp->id
                        ]);
                    }
                }
            }
        }
    }

    private function getOrCreateDraftWeeklyPlan(int $week, int $finca_id, string $year)
    {
        $plan = $this->plans->where('week', $week)->where('finca_id', $finca_id)->where('year', $year)->first();

        if (!$plan) {
            $plan = DraftWeeklyPlan::create([
                'week' => $week,
                'finca_id' => $finca_id,
                'year' => $year
            ]);
            $this->plans->push($plan);
        }

        return $plan;
    }

    private function getRecipe($recipe)
    {
        $recipe = $this->recipes->where('name', $recipe)->first();
        if (!$recipe) {
            throw new HttpException(404, "Receta {$recipe} no encotrado");
        }
        return $recipe;
    }

    private function getCrop($crop)
    {
        $crop = $this->crops->where('code', $crop)->first();
        if (!$crop) {
            throw new HttpException(404, "Cultivo {$crop} no encotrado");
        }
        return $crop;
    }

    private function getCdp(string $cdp_name, $lote, $startDate, $endDate, $recipe, $crop)
    {
        try {
            $cdp = $this->cdps->where('name', $cdp_name)->where('lote_id', $lote->id)->first();
            if (!$cdp) {
                $cdp = PlantationControl::create([
                    'name' => $cdp_name,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'lote_id' => $lote->id,
                    'total_plants' => $lote->total_plants,
                    'recipe_id' => $recipe->id,
                    'crop_id' => $crop->id,
                    'total_plants' => $lote->total_plants
                ]);
                $this->cdps->push($cdp);
            }

            return $cdp;
        } catch (\Throwable $th) {
            throw new HttpException(500, $th->getMessage());
        }
    }

    private function getLote(string $lote_name)
    {
        $lote = $this->lotes->where('name', $lote_name)->first();
        if (!$lote) {
            throw new HttpException(404, "Lote {$lote} no encontrado");
        }
        return $lote;
    }

    private function getTasks(int $recipe_id, int $crop_id, int $finca_id, int $week)
    {
        $tasks = $this->tasks->where('recipe_id', $recipe_id)->where('crop_id', $crop_id)->where('finca_id', $finca_id)->where('week', $week);

        return $tasks;
    }
}
