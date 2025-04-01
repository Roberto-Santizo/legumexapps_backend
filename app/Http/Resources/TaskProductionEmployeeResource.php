<?php

namespace App\Http\Resources;

use App\Models\BiometricTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskProductionEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $active = 0;
        $today = Carbon::today();
        $entrance = BiometricTransaction::where('pin', $this->position)->whereDate('event_time',$today)->first();

        if ($entrance) {
            $active = 1;
        }

        return [
            'id' => strval($this->id),
            'name' => $this->name,
            'code' => $this->code,
            'position' => $this->position,
            'column_id' => strval(1),
            'active' => $active
        ];
    }
}
