<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscalationLevel extends Model
{
    protected $fillable = [
        'level',
        'role_id',
        'escalate_after_hours'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
