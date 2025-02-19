<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    protected $fillable = [
        'name',
        'tolerance_percentage',
        'product_id',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
