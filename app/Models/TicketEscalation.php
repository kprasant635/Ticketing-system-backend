<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEscalation extends Model
{
    protected $fillable = [
        'ticket_id',
        'level',
        'escalated_to',
        'escalated_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }
}
