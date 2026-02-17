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

        if ($cdp) {
            $checklist = LoteChecklist::where('plantation_control_id', $cdp->id)->whereDate('created_at', Carbon::today())->first();
        } else {
            $checklist = null;
        }
        $flag = $checklist ? true : false;

        return [
            'id' => strval($this->id),
            'name' => $this->name,
            'finca' => $this->finca->name,
            'date' => $checklist ? $checklist->created_at->locale('es')->diffForHumans() : '',
            'size' => 0,
            'total_plants' => 0,
            'flag' => $flag
        ];
    }
}
