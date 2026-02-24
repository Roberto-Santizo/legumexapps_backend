<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteChecklist extends Model
{
    protected $fillable = [
        'plantation_control_id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
