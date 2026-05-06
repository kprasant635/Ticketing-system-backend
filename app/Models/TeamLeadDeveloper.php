<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamLeadDeveloper extends Model
{
    protected $table = 'team_lead_developer';

    protected $fillable = [
        'team_lead_id',
        'developer_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}