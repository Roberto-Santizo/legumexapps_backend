<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineStockKeepingUnits extends Model
{
    protected $fillable = [
        'sku_id',
        'line_id',
        'lbs_performance',
        'accepted_percentage',
        'payment_method'
    ];

    public function sku()
    {
        return $this->belongsTo(StockKeepingUnit::class,'sku_id','id');
    }
    
    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
