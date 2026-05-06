<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketQuery extends Model
{
    protected $fillable = [
        'ticket_id',
        'raised_by',
        'query_message',
        'replied_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function responses()
    {
        return $this->hasMany(
            TicketQueryResponse::class,
            'query_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }
}
