<?php

namespace App\Services;

use App\DTO\TicketDTO;
use App\Models\TicketAttachment;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Support\Facades\Storage;

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
        $data = [
            'ticket_no' => uniqid(),
            'applicant_id' => 1,
            'service_id' => $dto->service_id,
            'category_id' => $dto->category_id,
            'subcategory_id' => $dto->sub_category_id,
            'subject' => $dto->subject,
            'description' => $dto->description,
            'status_id' => 1,
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
}
