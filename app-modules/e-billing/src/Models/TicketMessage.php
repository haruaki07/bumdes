<?php

namespace Modules\EBilling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;

class TicketMessage extends Model implements MediableInterface
{
    use HasFactory, Mediable;

    protected $table = 'ebil_ticket_messages';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'author_name',
        'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
