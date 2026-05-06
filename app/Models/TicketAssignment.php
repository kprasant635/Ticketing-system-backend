<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAssignment extends Model
{
    protected $fillable = [
        'ticket_id',
        'status_id',
        'assigned_by',
        'assigned_to',
        'assigned_at'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
