<?php

namespace App\Services;

use App\DTO\TicketDTO;
use App\Models\CategoryTeamLead;
use App\Models\TicketAttachment;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Str;

class TicketService
{
    public function __construct(
        protected TicketRepositoryInterface $repo
    ) {}

    public function list()
    {
        return $this->repo->all();
    }

    public function create(TicketDTO $dto)
    {
        $teamLead = CategoryTeamLead::where('category_id', $dto->category_id)->first();
        $teamLeadId = $teamLead ? $teamLead->team_lead_id : null;
        $data = [
            'ticket_no' => 'TK-' . strtoupper(Str::random(6)),
            'applicant_id' => auth()->id(),
            'service_id' => $dto->service_id,
            'category_id' => $dto->category_id,
            'subcategory_id' => $dto->sub_category_id,
            'subject' => $dto->subject,
            'description' => $dto->description,
            'status_id' => 1,
            'team_lead_id' => $teamLeadId,
            'json_data' => [
                'applicant_name' => $dto->applicant_name,
                'phone_number' => $dto->applicant_phone,
                'email' => $dto->applicant_email,
            ]
        ];

        $ticket = $this->repo->create($data);

        if (!empty($dto->files)) {
            foreach ($dto->files as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return $ticket;
    }

    public function find(int $id)
    {
        return $this->repo->find($id);
    }

    public function update(int $id, TicketDTO $dto)
    {
        $data = [
            'service_id' => $dto->service_id,
            'category_id' => $dto->category_id,
            'subcategory_id' => $dto->sub_category_id,
            'subject' => $dto->subject,
            'description' => $dto->description,
            'json_data' => [
                'applicant_name' => $dto->applicant_name,
                'phone_number' => $dto->applicant_phone,
                'email' => $dto->applicant_email,
            ]
        ];

        $ticket = $this->repo->update($id, $data);

        if (!empty($dto->files)) {
            foreach ($dto->files as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return $ticket;
    }

    public function delete(int $id)
    {
        return $this->repo->delete($id);
    }

    public function addFiles(int $id, array $files)
    {
        $ticket = $this->repo->find($id);

        foreach ($files as $file) {
            $path = $file->store('tickets/attachments', 'public');
            $ticket->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
            ]);
        }

        return $ticket->load('attachments');
    }

    public function assign(int $id, int $developerId)
    {
        $ticket = $this->repo->find($id);

        // 🕒 SLA Logic: Auto-set SLA on assignment
        $priorityId = $ticket->priority_id;
        $resolutionHours = 24;  // Default fallback

        if ($priorityId) {
            $sla = \App\Models\Sla::where('priority_id', $priorityId)->first();
            if ($sla) {
                $resolutionHours = $sla->resolution_time_hours;
            } elseif ($ticket->priority) {
                $resolutionHours = $ticket->priority->sla_hours;
            }
        }

        $startTime = now();
        $dueTime = $startTime->copy()->addHours($resolutionHours);

        $ticket->update([
            'developer_id' => $developerId,
            'status_id' => 2,  // Assuming 2 is 'Assigned/Pending'
            'assigned_at' => $startTime
        ]);

        // Create or update Ticket SLA record
        $ticket->sla()->updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'start_time' => $startTime,
                'due_time' => $dueTime,
            ]
        );

        $ticket->assignments()->create([
            'assigned_by' => auth()->id(),
            'assigned_to' => $developerId,
            'status_id' => 2,
            'assigned_at' => $startTime
        ]);

        $ticket->logs()->create([
            'user_id' => auth()->id(),
            'status_id' => 2,
            'action' => 'Assigned to developer',
            'remark' => 'Ticket assigned to developer'
        ]);

        return $ticket->load(['logs.status', 'assignments.status', 'sla']);
    }

    public function changeStatus(int $id, int $statusId, string $remark = null)
    {
        $ticket = $this->repo->find($id);
        $ticket->update(['status_id' => $statusId]);

        $ticket->logs()->create([
            'user_id' => auth()->id(),
            'status_id' => $statusId,
            'remark' => $remark,
            'action' => 'Status changed'
        ]);

        return $ticket->load('logs.status');
    }

    public function close(int $id, string $remark = null)
    {
        $ticket = $this->repo->find($id);
        $ticket->update([
            'status_id' => 5,  // Assuming 5 is 'Closed'
            'closed_at' => now()
        ]);

        $ticket->logs()->create([
            'user_id' => auth()->id(),
            'status_id' => 5,
            'remark' => $remark,
            'action' => 'Ticket closed'
        ]);

        return $ticket->load('logs.status');
    }
}
