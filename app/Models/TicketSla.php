<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSla extends Model
{
    protected $fillable = [
        'ticket_id',
        'start_time',
        'due_time',
        'completed_time',
        'is_breached'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
