<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'service_id',
        'status'
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /*
     * |--------------------------------------------------------------------------
     * | Relationships
     * |--------------------------------------------------------------------------
     */

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function teamLeads()
    {
        return $this->belongsToMany(
            User::class,
            'category_team_lead',
            'category_id',
            'team_lead_id'
        );
    }

    public function projectCoordinators()
    {
        return $this->hasMany(ProjectCoordinatorMapping::class);
    }
}
