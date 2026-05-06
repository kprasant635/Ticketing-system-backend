<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCoordinatorMapping extends Model
{
    protected $table = 'project_coordinator_mapping';

    protected $fillable = [
        'project_coordinator_id',
        'service_id',
        'category_id'
    ];

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'project_coordinator_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}