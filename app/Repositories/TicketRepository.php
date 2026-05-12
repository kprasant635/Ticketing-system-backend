<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    public function all()
    {
        $user = auth()->user();
        $query = Ticket::with(['service', 'category', 'subcategory', 'priority', 'status', 'developer', 'applicant'])
            ->latest();

        if ($user) {
            // Role name is stored as a JSON array string like '["teamlead", "applicant"]'
            $roles = json_decode($user->role_name, true) ?? [];

            if (in_array('superadmin', $roles) || in_array('admin', $roles)) {
                // Admin sees all
            } elseif (in_array('teamlead', $roles)) {
                $query->where('team_lead_id', $user->id);
            } elseif (in_array('developer', $roles)) {
                $query->where('developer_id', $user->id);
            } elseif (in_array('applicant', $roles) || in_array('lra', $roles) || in_array('adc', $roles)) {
                $query->where('applicant_id', $user->id);
            }
        }

        return $query->get();
    }

    public function create(array $data)
    {
        return Ticket::create($data);
    }

    public function find(int $id)
    {
        return Ticket::with([
            'service',
            'category',
            'subcategory',
            'priority',
            'status',
            'attachments',
            'logs.status_id',
            'assignments.status_id'
        ])->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $ticket = $this->find($id);

        $ticket->update($data);

        return $ticket;
    }

    public function delete(int $id)
    {
        return Ticket::destroy($id);
    }

    public function getTeamLeadTickets($teamLeadId)
    {
        return Ticket::with([
            'service',
            'category',
            'subcategory',
            'priority',
            'status',
            'attachments',
            'logs.status_id',
            'assignments.status_id'
        ])->where('team_lead_id', $teamLeadId)->latest()->get();
    }
}
