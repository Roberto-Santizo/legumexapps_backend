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

        $checklist = LoteChecklist::where('plantation_control_id', $this->cdp->id)->whereDate('created_at', Carbon::today())->first();
        $flag = $checklist ? true : false;

        return [
            'id' => strval($this->id),
            'name' => $this->name,
            'finca' => $this->finca->name,
            'size' => 0,
            'total_plants' => 0,
            'flag' => $flag
        ];
    }
}
