<?php

namespace App\Http\Resources;

use App\Models\LoteChecklist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cdp = $this->cdp->last();

        $checklists = collect();

        if ($cdp) {
            $checklists = LoteChecklist::with('user')
                ->where('plantation_control_id', $cdp->id)
                ->whereDate('created_at', Carbon::today())
                ->get();
        }

        $nombres = $checklists
            ->pluck('user.name')
            ->filter()
            ->unique()
            ->implode(', ');

        $lastChecklist = $checklists->last();

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'finca' => $this->finca->name,
            'validation_by' => $nombres,
            'date' => $lastChecklist ? $lastChecklist->created_at->locale('es')->diffForHumans() : '',
            'size' => 0,
            'total_plants' => 0,
            'flag' => $checklists->isNotEmpty(),
        ];
    }
}
