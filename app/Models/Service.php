<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'status',
        'description'
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Relationships
     * |--------------------------------------------------------------------------
     */

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subadmins()
    {
        return $this->belongsToMany(
            User::class,
            'subadmin_service',
            'service_id',
            'subadmin_id'
        );
    }

    public function projectCoordinators()
    {
        return $this->hasMany(ProjectCoordinatorMapping::class);
    }
}
