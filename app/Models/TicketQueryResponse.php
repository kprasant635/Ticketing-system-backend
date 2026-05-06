<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketQueryResponse extends Model
{
    protected $fillable = [
        'query_id',
        'responded_by',
        'response_message'
    ];

    public function query()
    {
        return $this->belongsTo(
            TicketQuery::class,
            'query_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'responded_by'
        );
    }
}
