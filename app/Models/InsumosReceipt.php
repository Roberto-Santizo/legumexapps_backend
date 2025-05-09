<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumosReceipt extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',
        'supervisor_name',
        'invoice',
        'received_date',
        'invoice_date',
        'user_signature',
        'supervisor_signature'
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'invoice_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(InsumosReceiptsDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierPackingMaterial::class,'supplier_id','id');
    }
}
