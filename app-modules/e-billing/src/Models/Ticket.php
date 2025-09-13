<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EBilling\Enums\TicketPriority;
use Modules\EBilling\Enums\TicketStatus;

class Ticket extends Model
{
    use Datatable, HasFactory, SoftDeletes;

    protected $table = 'ebil_tickets';

    protected $fillable = [
        'code',
        'customer_id',
        'subject',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'last_activity_at',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected $dataTableColumns = [
        'code' => 'searchable|sortable',
        'subject' => 'searchable|sortable',
        'status' => 'searchable|sortable',
        'priority' => 'searchable|sortable',
        'customer.name' => 'searchable|sortable',
        'created_at' => 'sortable',
    ];

    protected static function booted()
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->code)) {
                $ticket->code = self::generateCode();
            }
            $ticket->last_activity_at = now();
        });
    }

    public static function generateCode(): string
    {
        $prefix = 'TCK';
        $seq = strtoupper(uniqid());

        return $prefix.'-'.substr($seq, -8);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
