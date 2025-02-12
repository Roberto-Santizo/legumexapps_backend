<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmReception extends Model
{
    protected $fillable = [
        'grn',
        'doc_date'
    ];


    public function field_data()
    {
        return $this->hasOne(FieldDataReception::class);
    }
}
