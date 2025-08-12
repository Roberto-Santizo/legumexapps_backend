<?php

namespace App\Http\Resources;

use App\Models\TaskProductionDraft;
use App\Models\WeeklyProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftProductionPlanResourceDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plan_exists = WeeklyProductionPlan::where('year', $this->year)->where('week', $this->week)->first();
        $flag_tasks = $this->tasks->count() == 0 ? true : false;
        $query = TaskProductionDraft::query();
        $query->where('draft_weekly_production_plan_id', $this->id);

        if ($request->query('sku')) {
            $query->whereHas('sku', function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->query('sku') . '%');
            });
        }

        if ($request->query('product_name')) {
            $query->whereHas('sku', function ($q) use ($request) {
                $q->where('product_name', 'LIKE', '%' . $request->query('product_name') . '%');
            });
        }

        if ($request->query('line')) {
            $query->where('line_id', $request->query('line'));
        }

        return [
            'id' => strval($this->id),
            'year' => $this->year,
            'week' => $this->week,
            'plan_created' => $plan_exists ? true : false,
            'confirmation_date' => $this->confirmation_date ? $this->confirmation_date->format('d-m-Y h:i:s A') : '',
            'production_confirmation' => $this->production_confirmation ? true : false,
            'bodega_confirmation' => $this->bodega_confirmation  ? true : false,
            'logistics_confirmation' => $this->logistics_confirmation  ? true : false,
            'flag_tasks' => !$flag_tasks,
            'tasks' => TaskProductionDraftResource::collection($query->get())
        ];
    }
}
