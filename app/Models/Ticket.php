<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_no',
        'applicant_id',
        'service_id',
        'category_id',
        'subcategory_id',
        'priority_id',
        'team_lead_id',
        'developer_id',
        'status_id',
        'subject',
        'description',
        'json_data',
        'assigned_at',
        'started_at',
        'resolved_at',
        'closed_at'
    ];

    protected $casts = [
        'json_data' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function queries()
    {
        return $this->hasMany(TicketQuery::class);
    }

    public function sla()
    {
        return $this->hasOne(TicketSla::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }

    public function assignments()
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function escalations()
    {
        return $this->hasMany(TicketEscalation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
