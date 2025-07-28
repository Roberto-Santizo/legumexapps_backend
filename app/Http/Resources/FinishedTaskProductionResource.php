<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinishedTaskProductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $line_hours = round($this->start_date->diffInHours($this->end_date), 2);
        $hours_timeout = 0;

        if (!$this->line_sku->payment_method) {
            $performance_hours = $this->total_lbs_bascula / $this->line_sku->lbs_performance;
        } else {
            $performance_hours = $line_hours;
        }

        foreach ($this->timeouts as $timeout) {
            $hours = 0;
            if ($timeout->end_date) {
                $hours = $timeout->start_date->diffInHours($timeout->end_date);
            }
            $hours_timeout += $hours;
        }

        $summary = [
            'HLinea' => $line_hours,
            'HPlan' => round($this->total_hours ?? $line_hours, 2),
            'HRendimiento' => round($performance_hours, 2),
            'HTiemposMuertos' => round($hours_timeout, 2),
        ];


        $note = $this->note ? [
            'reason' => $this->note->reason,
            'action' => $this->note->action,
            'user' => $this->note->user->name
        ] : null;

        return [
            'id' => strval($this->id),
            'line' => $this->line->name,
            'sku' => $this->line_sku->sku->code,
            'sku_description' => $this->line_sku->sku->product_name,
            'client' => $this->line_sku->sku->client_name,
            'total_lbs' => $this->total_lbs,
            'total_lbs_produced' => $this->total_lbs_produced,
            'total_lbs_bascula' => $this->total_lbs_bascula,
            'destination' => $this->destination ?? 'SIN DESTINO ASOCIADO',
            'start_date' => $this->start_date->format('d-m-Y h:i:s A'),
            'end_date' => $this->end_date->format('d-m-Y h:i:s A'),
            'max_value' => max($summary),
            'is_minimum_require' => $this->is_minimum_require ? true : false,
            'summary' => $summary,
            'note' => $note,
            'timeouts' => TaskProductionTimeoutResource::collection($this->timeouts),
            'employees' => EmployeeTaskProductionDetailResource::collection($this->employees),
            'history_operation_date' => TaskOperationDateBitacoraResource::collection($this->operationDateChanges),
            'transactions' => PackingMaterialTransactionDetailsResource::collection($this->transactions),
            'wastages' => PackingMaterialWastageResource::collection($this->wastages)
        ];
    }
}
