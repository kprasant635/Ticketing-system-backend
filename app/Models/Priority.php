<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $fillable = [
        'priority_name',
        'sla_hours'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function sla()
    {
        return $this->hasOne(Sla::class);
    }
}
