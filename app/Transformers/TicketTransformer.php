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
            'applicant_info' => $ticket->json_data,
            'attachments' => $ticket->attachments->map(fn($a) => [
                'file_name' => $a->file_name,
                'file_url' => asset('storage/' . $a->file_path),
                'file_type' => $a->file_type,
            ]),
            'created_at' => $ticket->created_at
        ];
    }

    public static function collection(Collection $tickets): array
    {
        return $tickets->map(fn(Ticket $ticket) => self::transform($ticket))->toArray();
    }
}
