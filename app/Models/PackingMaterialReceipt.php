<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialReceipt extends Model
{
    protected $fillable = [
        'user_id',
        'supervisor_name',
        'invoice_date',
        'receipt_date',
        'user_signature',
        'supervisor_signature',
        'observations'
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'receipt_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PackingMaterialReceiptDetail::class,'pm_receipt_id','id');
    }
}
