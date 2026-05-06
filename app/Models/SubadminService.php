<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubadminService extends Model
{
    protected $table = 'subadmin_service';

    protected $fillable = [
        'subadmin_id',
        'service_id'
    ];

    public function subadmin()
    {
        return $this->belongsTo(User::class, 'subadmin_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}