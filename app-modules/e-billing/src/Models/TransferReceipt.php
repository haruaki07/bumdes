<?php

namespace Modules\EBilling\Models;

use Illuminate\Database\Eloquent\Model;

class TransferReceipt extends Model
{
    protected $table = 'ebil_transfer_receipts';

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
