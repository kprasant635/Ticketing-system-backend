<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTeamLead extends Model
{
    protected $table = 'category_team_lead';

    protected $fillable = [
        'category_id',
        'team_lead_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }
}