<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineStockKeepingUnits extends Model
{
    protected $fillable = [
        'sku_id',
        'line_id',
        'client_id',
        'lbs_performance'  
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
