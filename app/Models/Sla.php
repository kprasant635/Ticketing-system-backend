<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    protected $fillable = [
        'priority_id',
        'resolution_time_hours'
    ];

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
}
