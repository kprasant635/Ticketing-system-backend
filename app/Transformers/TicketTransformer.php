<?php

namespace App\Transformers;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class TicketTransformer
{
    public static function transform(Ticket $ticket): array
    {
        return [
            'id' => encrypt_id($ticket->id),
            'ticket_no' => $ticket->ticket_no,
            'subject' => $ticket->subject,
            'description' => $ticket->description,
            'service' => $ticket->service?->service_name,
            'service_id' => encrypt_id($ticket->service_id),
            'category' => $ticket->category?->name,
            'category_id' => encrypt_id($ticket->category_id),
            'subcategory' => $ticket->subcategory?->name,
            'subcategory_id' => encrypt_id($ticket->subcategory_id),
            'priority' => $ticket->priority?->priority_name,
            'status' => $ticket->status?->status_name,
            'developer_name' => $ticket->developer?->name,
            'developer_id' => encrypt_id($ticket->developer_id),
            'applicant_info' => $ticket->json_data,
            'attachments' => $ticket->attachments->map(fn($a) => [
                'file_name' => $a->file_name,
                'file_url' => asset('storage/' . $a->file_path),
                'file_type' => $a->file_type,
            ]),
            'created_at' => $ticket->created_at,
            'timeline' => self::buildTimeline($ticket),
            'sla' => $ticket->sla ? [
                'start_time' => $ticket->sla->start_time,
                'due_time' => $ticket->sla->due_time,
                'completed_time' => $ticket->sla->completed_time,
                'is_breached' => $ticket->sla->is_breached,
            ] : null,
        ];
    }

    private static function buildTimeline(Ticket $ticket): array
    {
        $timeline = [];

        // 1. Initial Creation
        $timeline[] = [
            'icon' => '🔵',
            'event' => 'Ticket created',
            'at' => $ticket->created_at,
            'accent' => true
        ];

        // 2. Status Changes from Logs
        if ($ticket->logs) {
            foreach ($ticket->logs as $log) {
                if ($log->status) {
                    $statusName = strtolower($log->status->status_name);
                    $icon = match ($statusName) {
                        'pending', 'in progress', 'assigned' => '🟣',
                        'resolved', 'closed' => '🟢',
                        'on hold' => '🟠',
                        'reopened' => '🟡',
                        default => '⚪',
                    };

                    $timeline[] = [
                        'icon' => $icon,
                        'event' => $log->action ?? "Ticket " . $log->status->status_name,
                        'at' => $log->created_at,
                        'accent' => true
                    ];
                }
            }
        }

        // Sort timeline by date
        usort($timeline, function ($a, $b) {
            return $a['at'] <=> $b['at'];
        });

        return $timeline;
    }

    public static function collection(Collection $tickets): array
    {
        return $tickets->map(fn(Ticket $ticket) => self::transform($ticket))->toArray();
    }
}
